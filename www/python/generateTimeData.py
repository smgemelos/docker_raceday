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


    sicard_ids = list(range(8639381,8639410))

    #sicard_ids = list(range(8639381,8639390))

    s = [400,600,300,450,500,550,600,400,360,800,1000]


    for sicard_id in sicard_ids:
        #print(line)

        print "Hit RETURN to continue."
        raw_input("?")

        stages = random.randint(7,10) #number of stages this id will complete

        stamp_code_start = 1
        stamp_code_finish = 11

        start_time = d.datetime.now()
        
        for i in range(1,stages+1):

            start_time_str = start_time.strftime("%Y-%m-%d %H:%M:%S")
            start_ms = random.randint(0,999)

            stagetime = d.timedelta(seconds=random.randint(s[i]-40,s[i]+40))
            finish_time = start_time + stagetime
            finish_time_str = finish_time.strftime("%Y-%m-%d %H:%M:%S")
            finish_ms = random.randint(0,999)

            readout_time = d.datetime.now()

            q1 = "INSERT INTO stamps (id_event,stamp_card_id,stamp_control_code,stamp_control_mode,stamp_readout_datetime,stamp_punch_datetime,stamp_punch_ms) "
            q2 = "VALUES ('%s','%s','%s','%s','%s','%s','%s')" % (1,sicard_id,stamp_code_start,19,readout_time,start_time,start_ms)
 
            query = q1 + q2
            racedb_cur.execute(query)

            q1 = "INSERT INTO stamps (id_event,stamp_card_id,stamp_control_code,stamp_control_mode,stamp_readout_datetime,stamp_punch_datetime,stamp_punch_ms) "
            q2 = "VALUES ('%s','%s','%s','%s','%s','%s','%s')" % (1,sicard_id,stamp_code_finish,20,readout_time,finish_time,finish_ms)
 
            query = q1 + q2
            racedb_cur.execute(query)

            stamp_code_start += 1
            stamp_code_finish += 11
            if stamp_code_finish == 110:
                stamp_code_finish = 100

            transfertime = d.timedelta(seconds=random.randint(1000,1200))
            start_time = finish_time + transfertime

        



    racedb_cur.close()
    racedb.close()

   
if __name__ == "__main__":
    main();