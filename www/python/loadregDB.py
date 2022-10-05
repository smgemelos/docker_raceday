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

    query = "DELETE FROM stamps"
    racedb_cur.execute(query)
    query = "ALTER TABLE stamps AUTO_INCREMENT = 1"
    racedb_cur.execute(query)

    query = "DELETE FROM raceresults"
    racedb_cur.execute(query)
    query = "ALTER TABLE raceresults AUTO_INCREMENT = 1"
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
        q2 = "VALUES ('%s','%s','%s','%s','%s','','No')" % (name,riderid,raceid,plate,category)
 
        query = q1 + q2
        racedb_cur.execute(query)

    query = "DELETE FROM categories"
    racedb_cur.execute(query)
    query = "ALTER TABLE categories AUTO_INCREMENT = 1"
    racedb_cur.execute(query)

    query = "SELECT * FROM categories WHERE raceid='%s' ORDER BY sortorder " % (raceid)
    cesdb_cur.execute(query)

    for row in cesdb_cur.fetchall():
            
        name = pymysql.escape_string(row['name'])
        raceid = row['raceid']
        sortorder = row['sortorder']
        cat = row['cat']
        gender = row['gender']
        stages = row['stages']
        starttime = row['STARTTIME']

        q1 = "INSERT INTO categories (name,raceid,sortorder,cat,gender,stages,starttime) "
        q2 = "VALUES ('%s','%s','%s','%s','%s','%s','%s')" % (name,raceid,sortorder,cat,gender,stages,starttime)
 
        query = q1 + q2
        racedb_cur.execute(query)


    racedb_cur.close()
    racedb.close()
    cesdb_cur.close()
    cesdb.close()
    print "...Done - Click on Home to go back."

   
if __name__ == "__main__":
    main();