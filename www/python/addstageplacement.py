import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql


def main():

    raceid = sys.argv[1]
    dbflag = sys.argv[2]

    # Connect to the database

    if dbflag == "local":
        racedb = pymysql.connect(host='localhost',
                                     user='root',
                                     password='root',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)

    else:
        racedb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
                                     user='cesuser',
                                     password='wvG-Tkd-huo-72S',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)
    racedb.autocommit(True)
    racedb_cur = racedb.cursor()

    #cesdb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
    #                     user='cesuser',
    #                     password='wvG-Tkd-huo-72S',
    #                     db='ces',
    #                     charset='latin1',
    #                     cursorclass=pymysql.cursors.DictCursor)
    #cesdb.autocommit(True)
    #cesdb_cur = cesdb.cursor()

    for i in ['1','2','3','4','5','6','7','8']:

        tval = 't'+i
        pval = 'p'+i
    

        query = "SELECT * FROM raceresults WHERE raceid='%s' AND %s > 0 ORDER BY category, %s" % (raceid,tval,tval)
        racedb_cur.execute(query)


        results = racedb_cur.fetchall()

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

            print query % data

            racedb_cur.execute(query,data)  





   
if __name__ == "__main__":
    main();