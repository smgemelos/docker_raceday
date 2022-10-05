''' 
Use this script to load the registration data from the RiderID portal to the localhost DB for raceday timing.

Execution:

python loadregDB.py <racenumber i.e. 20151>

'''


import os, sys
import pymysql,time



def main():

    raceid = sys.argv[1]

    # Connect to the database
    cesdb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
                             user='cesuser',
                             password='wvG-Tkd-huo-72S',
                             db='ces',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    cesdb.autocommit(True)
    cesdb_cur = cesdb.cursor()

    racedb = pymysql.connect(host='localhost',
                             user='root',
                             password='root',
                             db='lcsportident_events',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    racedb.autocommit(True)
    racedb_cur = racedb.cursor()

    query = "DELETE FROM riders"
    racedb_cur.execute(query)
    query = "ALTER TABLE riders AUTO_INCREMENT = 1"
    racedb_cur.execute(query)

    query = "SELECT * FROM raceregistration WHERE raceid='%s' AND status='ACTIVE' ORDER BY plate " % (raceid)
    cesdb_cur.execute(query)

    for row in cesdb_cur.fetchall():
            
        name = pymysql.escape_string(row['name'])
        riderid = row['riderid']
        raceid = row['raceid']
        plate = row['plate']
        category = row['category']

        q1 = "INSERT INTO riders (name,riderid,raceid,plate,category,saic_returned,battery_check) "
        q2 = "VALUES ('%s','%s','%s','%s','%s','No','No')" % (name,riderid,raceid,plate,category)
 
        query = q1 + q2
        racedb_cur.execute(query)


    racedb_cur.close()
    racedb.close()
    cesdb_cur.close()
    cesdb.close()

   
if __name__ == "__main__":
    main();