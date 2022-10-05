import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

Standing = {}
Rider = {}


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

    # Clear the stamps DB
    query = "DELETE FROM `stamps`"
    racedb_cur.execute(query)
    query = "ALTER TABLE `stamps` AUTO_INCREMENT = 1"
    racedb_cur.execute(query)

    # Clear the livetiming file
    outputfile = open("../data.txt","w")
    outputfile.write('<div id="results"><form method="post"><table class="order-table table">\n')
    outputfile.write("<thead><tr><th><center>Plate</th><th><center>Name</th><th><center>Rider ID</th><th><center>Category</th><th><center>SIcard ID</th>")
    outputfile.write("</tr> </thead>\n")
    outputfile.close()
    

    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:


        query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
        racedb_cur.execute(query)


        UpdateList = {}


        for row in racedb_cur.fetchall():
            
            sicardid = row['stamp_card_id']
            control_code = row['stamp_control_code']
            control_mode = row['stamp_control_mode']
            stamp_type = row['stamp_type']
            read_datetime = row['stamp_readout_datetime']
            punch_time = row['stamp_punch_datetime']
            punch_time = punch_time.replace(year=2000, month=01, day=01)
            punch_ms = row['stamp_punch_ms']

            laststamp = read_datetime.strftime("%Y-%m-%d %H:%M:%S")

            #racenumber = str(l)

            # Check for matching riderid
            #if (riderid not in Rider):
            #    print "WARNING: Race ", l, " -  RiderID not found in DB: ", riderid," Reg Rider Name: ",row['name']
            #    continue

            UpdateList[sicardid] = 1

            

        if (UpdateList != {}):
            outputfile = open("../data.txt","w")
            outputfile.write('<div id="results"><form method="post"><table class="order-table table">\n')
            outputfile.write("<thead><tr><th><center>Plate</th><th><center>Name</th><th><center>Rider ID</th><th><center>Category</th><th><center>SIcard ID</th>")
            outputfile.write("</tr> </thead>\n")

            for i in UpdateList.keys():

                query = "SELECT * FROM `riders` WHERE `raceid`=1 AND `sicard_id`=%s" % i
                racedb_cur.execute(query)

                count = racedb_cur.rowcount

                if count == 0:
                    print "No entry found for SIcardID %s" % i
                    outputfile.write('<tr>\n')  
                    outputfile.write('<td colspan=5>No entry found for SIcardID %s - review/update details in Rider Checkin</td>' % i ) 
                    outputfile.write('</tr>')


                else:

                    print "SIcardID %s -- entries found:" % i

                    for row in racedb_cur.fetchall():

                        print "Plate: %s,  Rider Name: %s,  Category:  %s" % (row['plate'],row['name'],row['category'])

                        outputfile.write('<tr>\n')  
                        outputfile.write('<td>%s</td>\n' % row['plate'] ) 
                        outputfile.write('<td>%s</td>\n' % row['name'] ) 
                        outputfile.write('<td>%s</td>\n' % row['riderid'] ) 
                        outputfile.write('<td>%s</td>\n' % row['category'] )
                        outputfile.write('<td>%s</td>\n' % i )
                        outputfile.write('</tr>')
                
                #print "\n"

            outputfile.write('</table></form></div>\n')
            outputfile.close()
            

        time.sleep(1)
        #print("Running Rider Verification")


   
if __name__ == "__main__":
    main();