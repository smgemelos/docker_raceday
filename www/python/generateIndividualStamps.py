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


    sicard_id = 8666561

    s = [400,600,300,450,500,550,600,400,360,800,1000]

    fstage = [11, 22, 33, 44, 55, 66, 77, 88, 99]


    while True:
        #print(line)

        stage = input("Enter stage number (1-9):")

        stamp_code_start = int(stage)
        stamp_code_finish = fstage[int(stage)-1]

        start_time = d.datetime.now()

        start_time_str = start_time.strftime("%Y-%m-%d %H:%M:%S")
        start_ms = random.randint(0,999)

        stagetime = d.timedelta(seconds=random.randint(s[stage]-40,s[stage]+40))
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



    racedb_cur.close()
    racedb.close()

   
if __name__ == "__main__":
    main();