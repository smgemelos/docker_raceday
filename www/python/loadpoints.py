''' 
Use this script to load the registration data from the RiderID portal to the localhost DB for raceday timing.

Execution:

python loadregDB.py <racenumber i.e. 20151>

'''


import os, sys
import pymysql,time



def main():

    print ""
    print ""
    print ""
    print ""
    print "Loading Points Data..."

    # Connect to the database

    cesdb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
                             user='cesuser',
                             password='wvG-Tkd-huo-72S',
                             db='ces',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    
    #cesdb = pymysql.connect(host='localhost',
    #                         user='root',
    #                         password='root',
    #                         db='ces',
    #                         charset='latin1',
    #                         cursorclass=pymysql.cursors.DictCursor)
    
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

    query = "DELETE FROM points"
    racedb_cur.execute(query)
    query = "ALTER TABLE points AUTO_INCREMENT = 1"
    racedb_cur.execute(query)


    query = "SELECT * FROM points "
    cesdb_cur.execute(query)

    for row in cesdb_cur.fetchall():

        place = row['place']
        points = row['points']

        query = "INSERT INTO points (place,points) VALUES ('%s','%s')" % (place,points) 
        print query
        racedb_cur.execute(query)



    racedb_cur.close()
    racedb.close()
    cesdb_cur.close()
    cesdb.close()
    print "...Done - Click on Home to go back."

   
if __name__ == "__main__":
    main();