''' 
Use this script to load the registration data from the RiderID portal to the localhost DB for raceday timing.

Execution:

python loadregDB.py <racenumber i.e. 20151>

'''


import os, sys
import pymysql,time



def main():

    racefile = sys.argv[1]

    print ""
    print ""
    print ""
    print ""
    print "Loading Rider Names Data..."

    # Connect to the database
    connection = pymysql.connect(host='cesdb.californiaenduro.com',
                                     user='cesuser',
                                     password='wvG-Tkd-huo-72S',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)

    connection.autocommit(True)
    cur = connection.cursor()

    sourcefile = open(racefile,"r")
    firstline = sourcefile.readline()

    for line in sourcefile:
        #print(line)
        row = line.split(',')
        
        #racedate = time.strptime(row[1].strip(), "%m/%d/%y %H:%M")
        fname = row[0].strip()
        lname = row[1].strip()
        name = fname + " " + lname
        email = row[3].strip()
        

        query = "SELECT * FROM rider WHERE email='%s'" % (email)
        print query
        cur.execute(query)
        print cur.rowcount()

    cur.close()
    print "...Done - Click on Home to go back."

   
if __name__ == "__main__":
    main();