import os, math, sys, time
import datetime as d
import pymysql
import logging
 
# Create and configure logger
logging.basicConfig(filename="/var/log/ceslive.log",
                    format='%(asctime)s %(message)s',
                    filemode='w')
 
# Creating an object
logger = logging.getLogger()
 
# Setting the threshold of logger to DEBUG
logger.setLevel(logging.DEBUG)


def formattime(timestr):
    try:
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
    except:
        print("formattime error")
        return ""


def main():

    # Connect to the database

    print("Connecting to Race database")
    logger.info("Connecting to race database")
    try:
        racedb = pymysql.connect(host='mysql',
                                 user='sportident',
                                 password='sportident',
                                 db='lcsportident_events',
                                 charset='utf8',
                                 cursorclass=pymysql.cursors.DictCursor)
        racedb.autocommit(True)
        racedb_cur = racedb.cursor()
        print("Connected to Race database")
        logger.info("Connected to Race database")

        query = "UPDATE status SET ceslive='stop' WHERE id=1" 
        racedb_cur.execute(query)

    except pymysql.Error as e:
        print("Race DB Connection Failed - %d: %s" % (e.args[0],e.args[1]))
        logger.error("Race DB Connection Failed - %d: %s" % (e.args[0],e.args[1]))
        sys.exit("Race DB Connection Failed")  


    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        try:
            query = "SELECT * FROM status WHERE id=1" 
            racedb_cur.execute(query)
            state = racedb_cur.fetchone()
        except pymysql.Error as e:
            print("Race DB Status Update Failed - %d: %s" % (e.args[0],e.args[1]))
            logger.error("Race DB Status Update Failed - %d: %s" % (e.args[0],e.args[1]))

        if (state["ceslive"] == "stop"):
            #print("CES Live Stopped")
            logger.info("CES Live Stopped")

        elif (state["ceslive"] == "restart"):

            print("CES Live Restart Triggered")
            logger.info("CES Live Restart Triggered")
            laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

            print("Connecting to CES database")
            logger.info("Connecting to CES database")
            try:
                cesdb = pymysql.connect(host='cesdb.californiaenduro.com',
                             user='cesuser',
                             password='wvG-Tkd-huo-72S',
                             db='ces',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
                cesdb.autocommit(True)
                cesdb_cur = cesdb.cursor()
                logger.info("Connected to CES database")
            except pymysql.Error as e:
                print("CES DB Connection Failed - %d: %s" % (e.args[0],e.args[1]))
                logger.error("CES DB Connection Failed - %d: %s" % (e.args[0],e.args[1]))
                racedb.close()
                sys.exit("CES DB Connection Failed")  

            # Clear the CES Live Feed DB
            try:
                query = "DELETE FROM `liverace`"
                cesdb_cur.execute(query)
                query = "ALTER TABLE `liverace` AUTO_INCREMENT = 1"
                cesdb_cur.execute(query)

                query = "DELETE FROM `ceslivecats`"
                cesdb_cur.execute(query)
                query = "ALTER TABLE `ceslivecats` AUTO_INCREMENT = 1"
                cesdb_cur.execute(query)
            except pymysql.Error as e:
                print("ERROR: Failed to reset CES DB Live Results during restart - %d: %s" % (e.args[0],e.args[1]))
                logger.error("Failed to reset CES DB Live Results during restart - %d: %s" % (e.args[0],e.args[1]))
                racedb.close()
                sys.exit("Failed to reset CES DB Live Results during restart") 

            try:
                query = "SELECT name, sortorder, cat, gender, stages FROM categories"
                racedb_cur.execute(query)
                results = racedb_cur.fetchall()
            except pymysql.Error as e:
                print("ERROR: Failed get categories from Race DB during restart - %d: %s" % (e.args[0],e.args[1]))
                logger.error("Failed mysql read categories from Race DB during restart - %d: %s" % (e.args[0],e.args[1]))
                racedb.close()
                sys.exit("Failed mysql read categories from Race DB during restart") 

            
            for row in results:
                try:
                    query = "INSERT INTO ceslivecats (name, sortorder, cat, gender, stages) VALUES (%s,%s,%s,%s,%s)"
                    data = (row['name'],row['sortorder'],row['cat'],row['gender'],row['stages'])
                    cesdb_cur.execute(query,data)
                except pymysql.Error as e:
                    print("ERROR: CES DB Category Update - %d: %s" % (e.args[0],e.args[1]))
                    logger.error("ERROR: CES DB Category Update - %d: %s" % (e.args[0],e.args[1]))
                    racedb.close()
                    sys.exit("Failed mysql insert categories to CES DB during restart") 

            query = "UPDATE status SET ceslive='start' WHERE id=1" 
            racedb_cur.execute(query)

        elif (state["ceslive"] == "start"):

            try:
                query = "SELECT * FROM raceresults WHERE last_modified>'%s' ORDER BY last_modified" % laststamp
                racedb_cur.execute(query)
                results = racedb_cur.fetchall()
            except pymysql.Error as e:
                print("ERROR: Get results from Race DB - %d: %s" % (e.args[0],e.args[1]))
                logger.error("ERROR: Get results from Race DB  - %d: %s" % (e.args[0],e.args[1]))
                racedb.close()
                sys.exit("Mysql query failed getting results from Race DB") 

            for res in results:
                
                riderid = res['riderid']
                laststamp = res['last_modified'].strftime("%Y-%m-%d %H:%M:%S")

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
                    message = "Inserting live feed for %s" % res['name']
                    print(message)
                    logger.info(message)
                    try:
                        query = "INSERT INTO `liverace` (`sicard_id`,`plate`,`name`,`riderid`,`category`,`total`,`ranktotal`,`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`,`s9`,`s10`,`s11`,`s12`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
                        data = (res['sicard_id'],res['plate'],res['name'],res['riderid'],res['category'],formattime(res['total']),res['ranktotal'],
                                formattime(res['s1']),formattime(res['s2']),formattime(res['s3']),formattime(res['s4']),formattime(res['s5']),
                                formattime(res['s6']),formattime(res['s7']),formattime(res['s8']),formattime(res['s9']),formattime(res['s10']),
                                formattime(res['s11']),formattime(res['s12']))
                        cesdb_cur.execute(query,data)
                    except pymysql.Error as e:
                        print("ERROR: Results Insert Race DB - %d: %s \n\n" % (e.args[0],e.args[1]))
                        logger.error("ERROR: Results Insert Race DB  - %d: %s" % (e.args[0],e.args[1]))

                else:

                    message = "Updating live feed for %s" % res['name']
                    print(message)
                    logger.error(message)

                    try:
                        query = "UPDATE `liverace` SET `category`=%s,`total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`s9`=%s,`s10`=%s,`s11`=%s,`s12`=%s WHERE `riderid`=%s "
                        data = (res['category'],formattime(res['total']),res['ranktotal'],
                                formattime(res['s1']),formattime(res['s2']),formattime(res['s3']),formattime(res['s4']),formattime(res['s5']),
                                formattime(res['s6']),formattime(res['s7']),formattime(res['s8']),formattime(res['s9']),formattime(res['s10']),
                                formattime(res['s11']),formattime(res['s12']),riderid)
                        cesdb_cur.execute(query,data)  
                    except pymysql.Error as e:
                        print("ERROR: Results Update Race DB - %d: %s \n\n" % (e.args[0],e.args[1]))
                        logger.error("ERROR: Results Update Race DB  - %d: %s" % (e.args[0],e.args[1]))

            logger.info("waiting")

        time.sleep(10)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();