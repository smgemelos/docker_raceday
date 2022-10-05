''' 
Use this script to load the registration data from the RiderID portal to the localhost DB for raceday timing.

Execution:

python loadregDB.py <racenumber i.e. 20151>

'''


import os, sys
import pymysql,time



def main():

    raceid = sys.argv[1]

    print ""
    print ""
    print ""
    print ""
    print "Loading Registration Data..."

    # Connect to the database
    cesdb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
                             user='cesuser',
                             password='wvG-Tkd-huo-72S',
                             db='ces',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    cesdb.autocommit(True)
    cesdb_cur = cesdb.cursor()


    query = "UPDATE raceregistration SET plate=NULL WHERE raceid=%s" % (raceid)
    cesdb_cur.execute(query)

    query = "SELECT a.name, a.riderid, a.category, a.regtime, b.sortorder FROM raceregistration a, categories b WHERE a.raceid=%s AND b.raceid=%s AND a.status='ACTIVE' AND a.category=b.name ORDER BY b.sortorder, a.regtime" % (raceid,raceid)
    cesdb_cur.execute(query)


    platelist = range(72,100) + range(270,299) + range(332,351) + range(474,500) + range(573,596) + range(667,700)

    for row in cesdb_cur.fetchall():
        riderid = row['riderid']

        platenumber = platelist.pop(0)

        print platenumber

        query = "UPDATE raceregistration SET plate=%s WHERE raceid=%s AND riderid='%s'" % (platenumber,raceid,riderid)
        cesdb_cur.execute(query)




   
if __name__ == "__main__":
    main();