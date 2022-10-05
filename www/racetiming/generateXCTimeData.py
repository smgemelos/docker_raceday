''' 
This executable is used to load race results into the DB table "raceresults"

Execution:

python loadregDB.py <ragfile> <racenumber i.e. 20151>


'''


import os, sys
import pymysql,time, random
import datetime as d




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

    query = "DELETE FROM `stamps` WHERE id_event=1"
    racedb_cur.execute(query)


    sicard_ids = list(range(8639381,8639403))

    #sicard_ids = list(range(8639381,8639390))

    LapTime = 1800


    for sicard_id in sicard_ids:
        #print(line)

        print "Hit RETURN to continue."
        raw_input("?")

        laps = 1
        #laps = random.randint(4,8) #number of stages this id will complete

        stamp_code = 66


        timestamp = d.datetime.now()
        
        for i in range(1,laps+2):

            timestamp_str = timestamp.strftime("%Y-%m-%d %H:%M:%S")
            timestamp_ms = random.randint(0,999)

            readout_time = d.datetime.now()

            q1 = "INSERT INTO stamps (id_event,stamp_card_id,stamp_control_code,stamp_control_mode,stamp_readout_datetime,stamp_punch_datetime,stamp_punch_ms) "
            q2 = "VALUES ('%s','%s','%s','%s','%s','%s','%s')" % (1,sicard_id,stamp_code,18,readout_time,timestamp,timestamp_ms)
 
            query = q1 + q2
            racedb_cur.execute(query)

            if random.randint(0,1):
                duptimestamp = timestamp + d.timedelta(seconds=random.randint(10,200))
                duptimestamp_ms = random.randint(0,999)

                readout_time = d.datetime.now()

                q1 = "INSERT INTO stamps (id_event,stamp_card_id,stamp_control_code,stamp_control_mode,stamp_readout_datetime,stamp_punch_datetime,stamp_punch_ms) "
                q2 = "VALUES ('%s','%s','%s','%s','%s','%s','%s')" % (1,sicard_id,stamp_code,18,readout_time,duptimestamp,duptimestamp_ms)
     
                query = q1 + q2
                racedb_cur.execute(query)
                

            laptime = d.timedelta(seconds=random.randint(LapTime - 40,LapTime + 40))
            timestamp = timestamp + laptime



    racedb_cur.close()
    racedb.close()

   
if __name__ == "__main__":
    main();