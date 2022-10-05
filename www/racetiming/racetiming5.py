import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

Standing = {}
Rider = {}

Stages = ['s1','s2','s3','s4','s5','s6','s7','s8']

rankstart = 115200.0
Gates = [1,11,2,22,3,33,4,44,5,55,6,66,7,77,8,88]
cModes = [2,3,4,18,19,20]

def loadRiders(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM riders where sicard_id is not null"
    cur.execute(query)

    for row in cur.fetchall():

        riderid = row['riderid']
        sicard_id = row['sicard_id']
        ridername = row['name']
        category = row['category']
        plate = row['plate']


        if (sicard_id != ""):
            if (sicard_id in Dict):
                print("ERROR: Duplicate SIcardID: %s" % (sicard_id))
            else:
                Dict[sicard_id] = {"riderid":riderid, "ridername":ridername, "category":category, "sicard_id":sicard_id, "plate":plate}

    return Dict


def loadCatStages(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM categories"
    cur.execute(query)

    for row in cur.fetchall():

        name = row['name']
        stages = row['stages']

        Dict[name] = stages

    return Dict


def PointsTotal(a,N):
    total = 0
    a.sort(reverse=True)
    for i in range(N):
        total = total + a[i]
    return total


def calcStageTime(start,finish):
    if ( (start == 0) or (finish == 0) ):
        stagetime = ''
        return stagetime
    else:
        stagetime = finish - start
        return stagetime
    

def calcTotalTime(s):
    total = 0
    for i in s:
        if i != "":
            total = total + i
    return total

def calcRankTime(s):
    total = 0
    for i in s:
        if i != "":
            total = total + i
        else:
            total = total + 14400  #default stage time is 4 hrs
    return total


def timeStr(s):
    root = d.datetime(1950,1,1)
    ts = root + d.timedelta(seconds=(s))
    tmstr = ts.strftime("%H:%M:%S")
    millisec = str(ts.microsecond).zfill(6)[0:3]
    tstr = "%s.%s" % (tmstr,millisec)
    return tstr


def main():

    # Connect to the database
    racedb = pymysql.connect(host='localhost',
                             user='root',
                             password='root',
                             db='lcsportident_events',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    racedb.autocommit(True)
    racedb_cur = racedb.cursor()

    # Clear the results DB
    query = "DELETE FROM `raceresults`"
    racedb_cur.execute(query)
    query = "ALTER TABLE `raceresults` AUTO_INCREMENT = 1"
    racedb_cur.execute(query)

    # Clear the stamps DB
    #query = "DELETE FROM `stamps`"
    #racedb_cur.execute(query)
    #query = "ALTER TABLE `stamps` AUTO_INCREMENT = 1"
    #racedb_cur.execute(query)
            
    
    catStages = loadCatStages(racedb_cur)
    
    # Load all the riders from the riders DB table
    chipRiders = loadRiders(racedb_cur)

    for chip in chipRiders:

        #print chipRiders[chip]

        name = chipRiders[chip]['ridername']
        riderid = chipRiders[chip]['riderid']
        plate = chipRiders[chip]['plate']
        category = chipRiders[chip]['category']
        sicard_id = chipRiders[chip]['sicard_id']

        if (riderid in Standing):
            print("ERROR: Duplicate RiderID: %s" % (riderid))
        else:
            Standing[riderid] = {"riderid":riderid, "sicard_id":sicard_id, "ridername":name, "category":category, "plate":plate, "total":0, "rank":rankstart,
                                  "1":0, "11":0, "s1":"", 
                                  "2":0, "22":0, "s2":"",
                                  "3":0, "33":0, "s3":"",
                                  "4":0, "44":0, "s4":"",
                                  "5":0, "55":0, "s5":"",
                                  "6":0, "66":0, "s6":"",
                                  "7":0, "77":0, "s7":"",
                                  "8":0, "88":0, "s8":""
                                }
            
            query = "INSERT INTO `raceresults` (`plate`,`name`,`category`,`sicard_id`,`riderid`,`total`,`ranktotal`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
            data = (plate,name,category,sicard_id,riderid,"",rankstart,"","","","","","","","")
            racedb_cur.execute(query,data)

    #print Standing

    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        #print "Hit RETURN to continue."
        #raw_input("?")

        query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
        racedb_cur.execute(query)

        stamps = racedb_cur.fetchall()

        UpdateList = {}


        for stamp in stamps:

            ERROR = 0
            
            sicard_id = stamp['stamp_card_id']
            control_code = stamp['stamp_control_code']
            control_mode = stamp['stamp_control_mode']
            stamp_type = stamp['stamp_type']
            read_datetime = stamp['stamp_readout_datetime']
            punch_time = stamp['stamp_punch_datetime']
            punch_time = punch_time.replace(year=2000, month=01, day=01)
            punch_ms = stamp['stamp_punch_ms']

            laststamp = read_datetime.strftime("%Y-%m-%d %H:%M:%S")

            #print "Read stamp - SIcardID: ",sicard_id,"    code: ",control_code,"    mode: ",control_mode

            #racenumber = str(l)

            # Check for matching riderid
            #if (riderid not in Rider):
            #    print "WARNING: Race ", l, " -  RiderID not found in DB: ", riderid," Reg Rider Name: ",row['name']
            #    continue

            if (sicard_id in chipRiders):
                riderid = chipRiders[sicard_id]["riderid"]

            else:
                query = "SELECT * FROM `riders` WHERE `sicard_id`=%s" % sicard_id
                racedb_cur.execute(query)

                count = racedb_cur.rowcount

                if count == 0:
                    print "ERROR: No rider found for sicard_id %s in Riders table." % sicard_id
                    ERROR = 1
                elif count > 1:
                    print "ERROR: Multiple riders found for sicard_id %s in Riders table." % sicard_id
                    ERROR = 1
                else:
                    rider = racedb_cur.fetchone()
                    riderid = rider['riderid']
                    ridername = rider['name']
                    raceid = rider['raceid']
                    category = rider['category']
                    plate = rider['plate']

                    print "WARNING:  New SIcardID %s" % (sicard_id)

                    ''' Adding new SIcardID to the chipRiders dictionary '''
                    chipRiders[sicard_id] = {"riderid":riderid, "ridername":name, "category":category, "sicard_id":sicard_id, "plate":plate}

                    if (rider_id in Standing):

                        print "UPDATING: %s with new SIcardID %s" % (name,sicard_id)

                        ''' if the RiderID is already in the Standing dictionary, we need to update the SIcardID associated with that record - and update raceresults table.'''
                        Standing[riderid]["sicard_id"] = sicard_id

                        query = "UPDATE `raceresults` SET `sicard_id`=%s WHERE `riderid`=%s "
                        data = (sicard_id,riderid)
                        racedb_cur.execute(query,data)

                    else:

                        print "ADDING: %s with SIcardID %s" % (name,sicard_id)

                        ''' if the RiderID is NOT in the Standing dictionary, we need add a new entry to the Standing dictionary - and then add that to the raceresults table. '''
                        Standing[riderid] = {"riderid":riderid, "sicard_id":sicard_id, "ridername":name, "category":category, "plate":plate, "total":0, "rank":rankstart,
                                  "1":0, "11":0, "s1":"", 
                                  "2":0, "22":0, "s2":"",
                                  "3":0, "33":0, "s3":"",
                                  "4":0, "44":0, "s4":"",
                                  "5":0, "55":0, "s5":"",
                                  "6":0, "66":0, "s6":"",
                                  "7":0, "77":0, "s7":"",
                                  "8":0, "88":0, "s8":""
                                }
            
                        query = "INSERT INTO `raceresults` (`plate`,`name`,`category`,`sicard_id`,`riderid`,`total`,`ranktotal`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
                        data = (plate,name,category,sicard_id,riderid,"",rankstart,"","","","","","","","")
                        racedb_cur.execute(query,data)

                

            if not ERROR: 

                if (riderid in Standing):

                    if (control_code in Gates):

                       if ( control_mode in cModes ):

                            UpdateList[riderid] = 1

                            print "UPDATING: %s SIcardID %s" % (Standing[riderid]['ridername'],sicard_id)
                            print "Gate: %s  -- Time %s  msec %s" % (control_code,punch_time.strftime("%H:%M:%S"),punch_ms)
                            print ""

                            time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()
                            time2 = (time1*1000 + punch_ms)/1000
                            
                            cat = Standing[riderid]['category']

                            #Standing[riderid][str(control_code)] = time2 if not Standing[riderid][str(control_code)] else Standing[riderid][str(control_code)]

                            if (Standing[riderid][str(control_code)] == 0)
                                Standing[riderid][str(control_code)] = time2

                            s1 = calcStageTime(Standing[riderid]["1"],Standing[riderid]["11"]) if (catStages[cat] >= 1) else "" 
                            s2 = calcStageTime(Standing[riderid]["2"],Standing[riderid]["22"]) if (catStages[cat] >= 2) else ""
                            s3 = calcStageTime(Standing[riderid]["3"],Standing[riderid]["33"]) if (catStages[cat] >= 3) else ""
                            s4 = calcStageTime(Standing[riderid]["4"],Standing[riderid]["44"]) if (catStages[cat] >= 4) else ""
                            s5 = calcStageTime(Standing[riderid]["5"],Standing[riderid]["55"]) if (catStages[cat] >= 5) else ""
                            s6 = calcStageTime(Standing[riderid]["6"],Standing[riderid]["66"]) if (catStages[cat] >= 6) else ""
                            s7 = calcStageTime(Standing[riderid]["7"],Standing[riderid]["77"]) if (catStages[cat] >= 7) else ""
                            s8 = calcStageTime(Standing[riderid]["8"],Standing[riderid]["88"]) if (catStages[cat] >= 8) else ""

                            total = calcTotalTime([s1,s2,s3,s4,s5,s6,s7,s8])
                            #print "Total: " + str(total)
                            rank  = round(calcRankTime([s1,s2,s3,s4,s5,s6,s7,s8]),3)
                            #print rank

                            Standing[riderid]['s1'] = timeStr(s1) if (s1 != "") else "" 
                            Standing[riderid]['s2'] = timeStr(s2) if (s2 != "") else "" 
                            Standing[riderid]['s3'] = timeStr(s3) if (s3 != "") else "" 
                            Standing[riderid]['s4'] = timeStr(s4) if (s4 != "") else "" 
                            Standing[riderid]['s5'] = timeStr(s5) if (s5 != "") else "" 
                            Standing[riderid]['s6'] = timeStr(s6) if (s6 != "") else "" 
                            Standing[riderid]['s7'] = timeStr(s7) if (s7 != "") else "" 
                            Standing[riderid]['s8'] = timeStr(s8) if (s8 != "") else "" 
                            Standing[riderid]['total'] = timeStr(total)
                            Standing[riderid]['rank']  = rank
                else:
                    print "ERROR: RiderID %s Not Found in Standing dictionary" % riderid


        if (UpdateList != {}):

            for i in UpdateList.keys():

                query = "UPDATE `raceresults` SET `total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`t1`=%s,`t2`=%s,`t3`=%s,`t4`=%s,`t5`=%s,`t6`=%s,`t7`=%s,`t8`=%s,`t11`=%s,`t22`=%s,`t33`=%s,`t44`=%s,`t55`=%s,`t66`=%s,`t77`=%s,`t88`=%s WHERE `riderid`=%s "
                data = (Standing[i]['total'],Standing[i]['rank'],Standing[i]['s1'],Standing[i]['s2'],Standing[i]['s3'],Standing[i]['s4'],Standing[i]['s5'],Standing[i]['s6'],Standing[i]['s7'],Standing[i]['s8'],Standing[i]['1'],Standing[i]['2'],Standing[i]['3'],Standing[i]['4'],Standing[i]['5'],Standing[i]['6'],Standing[i]['7'],Standing[i]['8'],Standing[i]['11'],Standing[i]['22'],Standing[i]['33'],Standing[i]['44'],Standing[i]['55'],Standing[i]['66'],Standing[i]['77'],Standing[i]['88'],Standing[i]['riderid'])
                racedb_cur.execute(query,data)

        time.sleep(1)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();