import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

Standing = {}
Rider = {}

def loadRiders(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM riders WHERE raceid=1"
    cur.execute(query)

    for row in cur.fetchall():

        riderid = row['riderid']
        sicard_id = row['sicard_id']
        ridername = row['name']
        category = row['category']
        plate = row['plate']

        Dict[sicard_id] = {"riderid":riderid, "ridername":ridername, "category":category, "riderid":riderid, "sicard_id":sicard_id, "plate":plate}

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
            total = total + 6039
    return total


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
    query = "DELETE FROM `stamps`"
    racedb_cur.execute(query)
    query = "ALTER TABLE `stamps` AUTO_INCREMENT = 1"
    racedb_cur.execute(query)

    # Clear the livetiming file
    outputfile = open("../data.txt","w")
    outputfile.write('<div id="results"><form method="post"><table class="order-table table">\n')
    outputfile.write("<thead><tr><th><center>Name</th><th><center>Category</th><th><center>Total</th> <th><center>S1 Time</th><th><center>S2 Time</th><th><center>S3 Time</th><th><center>S4 Time</th></tr> </thead>\n")
    outputfile.write('</table></form></div>\n')
    outputfile.close()

    rankstart = 48312.0
    Gates = [1,11,2,22,3,33,4,44,5,55,6,66,7,77,8,88]
    Stages = 5

    # Load all the riders from the riders DB table
    query = "SELECT * FROM `riders` WHERE `raceid`=1"
    racedb_cur.execute(query)
    

    for row in racedb_cur.fetchall():
            
        name = row['name']
        riderid = row['riderid']
        raceid = row['raceid']
        plate = row['plate']
        category = row['category']
        sicard_id = row['sicard_id']

        if (sicard_id in Standing):
            print("ERROR: Duplicate SIcardID: %s" % (sicard_id))
        else:
            Standing[sicard_id] = {"riderid":riderid, "ridername":name, "category":category, "plate":plate, "total":0, "rank":0,
                                  "1":0, "11":0, "s1":"", 
                                  "2":0, "22":0, "s2":"",
                                  "3":0, "33":0, "s3":"",
                                  "4":0, "44":0, "s4":"",
                                  "5":0, "55":0, "s5":"",
                                  "6":0, "66":0, "s6":"",
                                  "7":0, "77":0, "s7":"",
                                  "8":0, "88":0, "s8":""
                                }
            
            query = "INSERT INTO `raceresults` (`plate`,`name`,`category`,`riderid`,`total`,`ranktotal`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
            data = (plate,name,category,riderid,"",rankstart,"","","","","","","","")
            racedb_cur.execute(query,data)

    #print Standing

    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        print "Hit RETURN to continue."
        raw_input("?")

        

        query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
        racedb_cur.execute(query)

        UpdateList = {}


        for row in racedb_cur.fetchall():
            
            sicard_id = row['stamp_card_id']
            control_code = row['stamp_control_code']
            control_mode = row['stamp_control_mode']
            stamp_type = row['stamp_type']
            read_datetime = row['stamp_readout_datetime']
            punch_time = row['stamp_punch_datetime']
            punch_time = punch_time.replace(year=2000, month=01, day=01)
            punch_ms = row['stamp_punch_ms']

            laststamp = read_datetime.strftime("%Y-%m-%d %H:%M:%S")

            #racenumber = str(l)

            # Check for matching riderid
            #if (riderid not in Rider):
            #    print "WARNING: Race ", l, " -  RiderID not found in DB: ", riderid," Reg Rider Name: ",row['name']
            #    continue


            if (sicard_id in Standing):

                if control_code in Gates:

                    UpdateList[sicard_id] = 1

                    if ( (control_mode == 19) or (control_mode == 20) ):

                        time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()
                        time2 = (time1*1000 + punch_ms)/1000
                        
                        Standing[sicard_id][str(control_code)] = time2
                        s1 = calcStageTime(Standing[sicard_id]["1"],Standing[sicard_id]["11"])
                        s2 = calcStageTime(Standing[sicard_id]["2"],Standing[sicard_id]["22"])
                        s3 = calcStageTime(Standing[sicard_id]["3"],Standing[sicard_id]["33"])
                        s4 = calcStageTime(Standing[sicard_id]["4"],Standing[sicard_id]["44"])
                        s5 = calcStageTime(Standing[sicard_id]["5"],Standing[sicard_id]["55"])
                        s6 = calcStageTime(Standing[sicard_id]["6"],Standing[sicard_id]["66"])
                        s7 = calcStageTime(Standing[sicard_id]["7"],Standing[sicard_id]["77"])
                        s8 = calcStageTime(Standing[sicard_id]["8"],Standing[sicard_id]["88"])

                        total = calcTotalTime([s1,s2,s3,s4,s5,s6,s7,s8])
                        #print "Total: " + str(total)
                        rank  = round(calcRankTime([s1,s2,s3,s4,s5,s6,s7,s8]),3)
                        #print rank

                        Standing[sicard_id]['s1'] = str(d.timedelta(seconds=s1)) if (s1 != "") else "" 
                        Standing[sicard_id]['s2'] = str(d.timedelta(seconds=s2)) if (s2 != "") else "" 
                        Standing[sicard_id]['s3'] = str(d.timedelta(seconds=s3)) if (s3 != "") else "" 
                        Standing[sicard_id]['s4'] = str(d.timedelta(seconds=s4)) if (s4 != "") else "" 
                        Standing[sicard_id]['s5'] = str(d.timedelta(seconds=s5)) if (s5 != "") else "" 
                        Standing[sicard_id]['s6'] = str(d.timedelta(seconds=s6)) if (s6 != "") else "" 
                        Standing[sicard_id]['s7'] = str(d.timedelta(seconds=s7)) if (s7 != "") else "" 
                        Standing[sicard_id]['s8'] = str(d.timedelta(seconds=s8)) if (s8 != "") else "" 
                        Standing[sicard_id]['total'] = str(d.timedelta(seconds=total))
                        Standing[sicard_id]['rank']  = rank
            else:
                print "ERROR: Unknown SIcardID"


        if (UpdateList != {}):
            outputfile = open("../data.txt","w")
            outputfile.write('<div id="results"><form method="post"><table class="order-table table">\n')
            outputfile.write("<thead><tr><th><center>Name</th><th><center>Category</th><th><center>Total</th>")
            
            outputfile.write("<th><center>S1 Time</th>")
            outputfile.write("<th><center>S2 Time</th>")
            outputfile.write("<th><center>S3 Time</th>")
            outputfile.write("<th><center>S4 Time</th>")

            outputfile.write("</tr> </thead>\n")

            for i in UpdateList.keys():
                outputfile.write('<tr>\n')  
                outputfile.write('<td>%s</td>\n' % Standing[i]['ridername'] ) 
                outputfile.write('<td>%s</td>\n' % Standing[i]['category'] ) 
                outputfile.write('<td>%s</td>\n' % Standing[i]['total'] ) 
                outputfile.write('<td>%s</td>\n' % Standing[i]['s1'] )
                outputfile.write('<td>%s</td>\n' % Standing[i]['s2'] )
                outputfile.write('<td>%s</td>\n' % Standing[i]['s3'] )
                outputfile.write('<td>%s</td>\n' % Standing[i]['s4'] )
                outputfile.write('</tr>')

                query = "UPDATE `raceresults` SET `total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s WHERE `riderid`=%s "
                data = (Standing[i]['total'],Standing[i]['rank'],Standing[i]['s1'],Standing[i]['s2'],Standing[i]['s3'],Standing[i]['s4'],Standing[i]['s5'],Standing[i]['s6'],Standing[i]['s7'],Standing[i]['s8'],Standing[i]['riderid'])
                racedb_cur.execute(query,data)


            outputfile.write('</table></form></div>\n')
            outputfile.close()

        time.sleep(1)
        print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();