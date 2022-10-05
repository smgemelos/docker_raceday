import os, math, sys, time
import datetime as d
import pymysql


def formattime(timestr):
    timearr = timestr.split(':')
    if len(timearr) == 3:
        timestr0 = "" if timearr[0] == '00' else timearr[0]+':'
        timestr1 = "" if timearr[1] == '00' else timearr[1]+':'
        timestr2 = "" if timearr[2] == '00.000' else timearr[2]
    else:
        timestr0 = ""
        timestr1 = ""
        timestr2 = ""

    return timestr0+timestr1+timestr2


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

    cesdb = pymysql.connect(host='cesdb.californiaenduro.com',
                         user='cesuser',
                         password='wvG-Tkd-huo-72S',
                         db='ces',
                         charset='latin1',
                         cursorclass=pymysql.cursors.DictCursor)
    cesdb.autocommit(True)
    cesdb_cur = cesdb.cursor()

    # Clear the CES Live Feed DB
    query = "DELETE FROM `liverace`"
    cesdb_cur.execute(query)
    query = "ALTER TABLE `liverace` AUTO_INCREMENT = 1"
    cesdb_cur.execute(query)

    query = "DELETE FROM `ceslivecats`"
    cesdb_cur.execute(query)
    query = "ALTER TABLE `ceslivecats` AUTO_INCREMENT = 1"
    cesdb_cur.execute(query)

    query = "SELECT name, sortorder, cat, gender, stages FROM categories"
    racedb_cur.execute(query)
    results = racedb_cur.fetchall()

    for row in results:

        query = "INSERT INTO ceslivecats (name, sortorder, cat, gender, stages) VALUES (%s,%s,%s,%s,%s)"
        data = (row['name'],row['sortorder'],row['cat'],row['gender'],row['stages'])
        cesdb_cur.execute(query,data)


    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        #print "Hit RETURN to continue."
        #raw_input("?")

        query = "SELECT * FROM raceresults WHERE last_modified>'%s' ORDER BY last_modified" % laststamp
        racedb_cur.execute(query)

        results = racedb_cur.fetchall()

        for res in results:
            
            riderid = res['riderid']
            laststamp = res['last_modified'].strftime("%Y-%m-%d %H:%M:%S")

            print "Updating live feed for %s" % res['name']

            '''
            Look for RiderID in the LiveRace db table - if it's not in the LiveRace table, we need to add it.
            '''
            query = "SELECT * FROM `liverace` WHERE `riderid`='%s'" % riderid
            cesdb_cur.execute(query)
            count = cesdb_cur.rowcount


            if (count == 0):
                '''
                If the RiderID does not currently exist in the LiveRace db - add a new row to the LiveRace table
                '''
                query = "INSERT INTO `liverace` (`sicard_id`,`plate`,`name`,`riderid`,`category`,`total`,`ranktotal`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`,`s9`,`s10`,`s11`,`s12`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
                data = (res['sicard_id'],res['plate'],res['name'],res['riderid'],res['category'],formattime(res['total']),res['ranktotal'],
                        formattime(res['s1']),formattime(res['s2']),formattime(res['s3']),formattime(res['s4']),formattime(res['s5']),
                        formattime(res['s6']),formattime(res['s7']),formattime(res['s8']),formattime(res['s9']),formattime(res['s10']),
                        formattime(res['s11']),formattime(res['s12']))
                cesdb_cur.execute(query,data)

            else:

                query = "UPDATE `liverace` SET `category`=%s,`total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`s9`=%s,`s10`=%s,`s11`=%s,`s12`=%s WHERE `riderid`=%s "
                data = (res['category'],formattime(res['total']),res['ranktotal'],
                        formattime(res['s1']),formattime(res['s2']),formattime(res['s3']),formattime(res['s4']),formattime(res['s5']),
                        formattime(res['s6']),formattime(res['s7']),formattime(res['s8']),formattime(res['s9']),formattime(res['s10']),
                        formattime(res['s11']),formattime(res['s12']),riderid)
                cesdb_cur.execute(query,data)  



        time.sleep(5)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();