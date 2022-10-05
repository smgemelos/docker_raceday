import os, math, sys, time
import datetime as d
#from dateutil import parser
import referencedb as ref
import pymysql

Standing = {}
Rider = {}



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

    #load_referencedata()
    global Rider
    Rider = ref.loadRiders(racedb_cur)

    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        query = "DELETE FROM `raceresults`"
        racedb_cur.execute(query)

        query = "ALTER TABLE `raceresults` AUTO_INCREMENT = 1"
        racedb_cur.execute(query)

        sicard_id = ""

        query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp

        racedb_cur.execute(query)


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

                if ( (control_mode == 19) or (control_mode == 20) ):

                    

                    time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()
                    time2 = (time1*1000 + punch_ms)/1000
                    
                    Standing[sicard_id][str(control_code)] = time2
                    s1 = calcStageTime(Standing[sicard_id]["1"],Standing[sicard_id]["11"])
                    s2 = calcStageTime(Standing[sicard_id]["2"],Standing[sicard_id]["22"])
                    s3 = calcStageTime(Standing[sicard_id]["3"],Standing[sicard_id]["33"])
                    s4 = calcStageTime(Standing[sicard_id]["4"],Standing[sicard_id]["44"])

                    total = calcTotalTime([s1,s2,s3,s4])
                    #print "Total: " + str(total)
                    rank  = calcRankTime([s1,s2,s3,s4])
                    #print "Rank: " + str(rank)

                    Standing[sicard_id]['s1'] = str(d.timedelta(seconds=s1)) if (s1 != "") else "" 
                    Standing[sicard_id]['s2'] = str(d.timedelta(seconds=s2)) if (s2 != "") else "" 
                    Standing[sicard_id]['s3'] = str(d.timedelta(seconds=s3)) if (s3 != "") else "" 
                    Standing[sicard_id]['s4'] = str(d.timedelta(seconds=s4)) if (s4 != "") else "" 
                    Standing[sicard_id]['total'] = str(d.timedelta(seconds=total))
                    Standing[sicard_id]['rank']  = str(d.timedelta(seconds=rank))


            else:
                Standing[sicard_id] = {"riderid":Rider[sicard_id]["riderid"], "ridername":Rider[sicard_id]["ridername"], "category":Rider[sicard_id]["category"], "total":0, "rank":0,
                                      "1":0, "11":0, "s1":"", 
                                      "2":0, "22":0, "s2":"",
                                      "3":0, "33":0, "s3":"",
                                      "4":0, "44":0, "s4":"",
                                      "5":0, "55":0, "s5":"",
                                      "6":0, "66":0, "s6":"",
                                      "7":0, "77":0, "s7":"",
                                      "8":0, "88":0, "s8":""
                                    }

                if ( (control_mode == 19) or (control_mode == 20) ):
                    time1 = (punch_time - d.datetime(1970, 1, 1)).total_seconds()
                    time2 = (time1*1000 + punch_ms)/1000
                    
                    Standing[sicard_id][str(control_code)] = time2

                    #print Standing[ridercat]


        if (sicard_id != ""):
            outputfile = open("../data.txt","w")
            outputfile.write('<div id="results"><form method="post"><table class="order-table table">\n')
            outputfile.write("<thead><tr><th><center>Name</th><th><center>Category</th><th><center>Total</th> <th><center>S1 Time</th><th><center>S2 Time</th><th><center>S3 Time</th><th><center>S4 Time</th></tr> </thead>\n")
            outputfile.write('<tr>\n')  
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['ridername'] ) 
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['category'] ) 
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['total'] ) 
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['s1'] )
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['s2'] )
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['s3'] )
            outputfile.write('<td>%s</td>\n' % Standing[sicard_id]['s4'] )
            outputfile.write('</tr></table></form></div>\n')
            outputfile.close()


        Place = 1
        PrevCat = ""

        for l in Standing.keys():

            if (PrevCat != Standing[l]['category']):
                Place = 1

      
            query = "INSERT INTO `raceresults` (`name`,`category`,`riderid`,`total`,`ranktotal`,`place`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
            #print query
            data = (Standing[l]['ridername'],Standing[l]['category'],Standing[l]['riderid'],Standing[l]['total'],Standing[l]['rank'],Place,Standing[l]['s1'],Standing[l]['s2'],Standing[l]['s3'],Standing[l]['s4'],Standing[l]['s5'],Standing[l]['s6'],Standing[l]['s7'],Standing[l]['s8'])
            racedb_cur.execute(query,data)

            PrevCat = Standing[l]['category']
            Place = Place + 1

        time.sleep(1)
        print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();