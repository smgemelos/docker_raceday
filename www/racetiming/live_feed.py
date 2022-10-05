import os, math, sys, time
import datetime as d
import pymysql


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

    cesdb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
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
                query = "INSERT INTO `liverace` (`sicard_id`,`plate`,`name`,`riderid`,`category`,`total`,`ranktotal`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
                data = (res['sicard_id'],res['plate'],res['name'],res['riderid'],res['category'],res['total'],res['ranktotal'],
                        res['s1'],res['s2'],res['s3'],res['s4'],res['s5'],res['s6'],res['s7'],res['s8'])
                cesdb_cur.execute(query,data)

            else:

                query = "UPDATE `liverace` SET `category`=%s,`total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s WHERE `riderid`=%s "
                data = (res['category'],res['total'],res['ranktotal'],
                        res['s1'],res['s2'],res['s3'],res['s4'],res['s5'],res['s6'],res['s7'],res['s8'],
                        riderid)
                cesdb_cur.execute(query,data)  



        time.sleep(5)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();