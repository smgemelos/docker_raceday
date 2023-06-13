''' 
This executable is used to load race results into the DB table "raceresults"

Execution:

python3 loadresultsDB_StagePlacement_StageWins.py 20239 aws


'''


import os, sys, math, time
import pymysql
import datetime as d


def calcSec(s):

    if (s == "") or (s == "None") or (s == "DNF"):
        return 0.0

    try:

        time = s.split(".")[0].split(":")

        millisec = s.split(".")[1]

        if len(time) == 3:
            sec = int(time[0])*60*60 + int(time[1])*60 + int(time[2])
        elif len(time) == 2:
            sec = int(time[0])*60 + int(time[1])
        elif len(time) == 1:
            sec = int(time[0])
        else:
            print("check this time %s" % s)

        #print "sec: %s  msec: %s" % (sec,millisec)

        time = "%s.%s" % (sec,millisec)

        return float(time)
    
    except:
        return 0.0


def main():

    # Connect to the database

    raceid = sys.argv[1]
    dbflag = sys.argv[2]

    if dbflag == "local":
        cesdb = pymysql.connect(host='localhost',
                                     user='sportident',
                                     password='sportident',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)

    else:
        cesdb = pymysql.connect(host='cesdb.californiaenduro.com',
                                     user='cesuser',
                                     password='wvG-Tkd-huo-72S',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)
    
    cesdb.autocommit(True)
    cesdb_cur = cesdb.cursor()


    print("")
    print("Adding Stage Placement....")


    for i in ['1','2','3','4','5','6','7','8','9','10','11','12']:

        tval = 't'+i
        pval = 'p'+i
    

        query = "SELECT * FROM raceresults WHERE raceid='%s' AND %s > 0 ORDER BY category, %s" % (raceid,tval,tval)
        cesdb_cur.execute(query)


        results = cesdb_cur.fetchall()

        cat = ""
        place = 0
        prevtime = 0

        for row in results:


            if row['category'] != cat:
                cat = row['category']
                place = 1
                prevtime = 0
            else:
                if row[tval] == prevtime:
                    place = place

                else:
                    place = place + 1

            
            query = "UPDATE raceresults SET "+pval+"=%s WHERE riderid=%s AND raceid=%s " 
            data = (place,row['riderid'],raceid)

            cesdb_cur.execute(query,data)  




    print("")
    print("Calculating Stage Wins....")


    query = "SELECT * FROM raceresults WHERE raceid='%s'" % raceid 
    cesdb_cur.execute(query)


    results = cesdb_cur.fetchall()

    '''
    Run through each of the new timestamps in the stamps TABLE
    '''
    for row in results:

        stagewins = 0
        for i in range(1,12):
            if (row['p'+str(i)] == 1):
                stagewins += 1

        query = "UPDATE `raceresults` SET `stagewins`=%s WHERE `id`=%s"
        data = (stagewins,row['id'])


        cesdb_cur.execute(query,data)  





    print("")
    print("DONE - Click Home to go back.")


    cesdb_cur.close()
    cesdb.close()
    

   
if __name__ == "__main__":
    main();