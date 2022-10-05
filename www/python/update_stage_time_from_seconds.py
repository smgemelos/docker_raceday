import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

def calcTotalTime(s):
    total = 0.0
    stages = 0
    for i in s:
        if (i != ''):
            total = total + float(i)
            if (i != 0):
                stages = stages + 1
    return total,stages

def calcSec(s):

    if (s == "") or (s == "None") or (s == "DNF"):
        return 0.0

    try:

        time = s.split(".")[0].split(":")

        millisec = s.split(".")[1]

        if len(time) == 3:
            sec = int(time[0])*60*60 + int(time[1])*60 + int(time[2])
        elif len(time) == 2:
            sec = int(time[0])*60 + int(time[1])
        elif len(time) == 1:
            sec = int(time[0])
        else:
            print "check this time %s" % s

        #print "sec: %s  msec: %s" % (sec,millisec)

        time = "%s.%s" % (sec,millisec)

        return float(time)
    
    except:
        return 0.0


def calcSec2(s):

    if (s == "") or (s == "None") or (s == "DNF"):
        return 0.0

    try:

        time = s.split(".")[0].split(":")

        millisec = s.split(".")[1]

        millisec2 = millisec.rstrip("0")

        millisec2 = "0"*(3-len(millisec2)) + millisec2

        #print "millisec: %s,  millisec2: %s" % (millisec,millisec2)

        if len(time) == 3:
            sec = int(time[0])*60*60 + int(time[1])*60 + int(time[2])
        elif len(time) == 2:
            sec = int(time[0])*60 + int(time[1])
        elif len(time) == 1:
            sec = int(time[0])
        else:
            print "check this time %s" % s

        #print "sec: %s  msec: %s" % (sec,millisec)

        time = "%s.%s" % (sec,millisec2)

        return float(time)
    
    except:
        return 0.0


def timeStr(s):
    if s == 0:
        return ""

    sec = float(s)

    root = d.datetime(1970,1,1)
    ts = root + d.timedelta(seconds=(sec))

    if sec >=3600 :
        tmstr = ts.strftime("%H:%M:%S")
    else :
        tmstr = ts.strftime("%M:%S")


    
    usec = ts.microsecond
    if (usec < 10000):
        usecstr = "00" + str(usec)
    elif (usec < 100000):
        usecstr = "0" + str(usec)
    else:
        usecstr = str(usec)

    millisec = usecstr[0:3]

    tstr = "%s.%s" % (tmstr,millisec)
    return tstr


def main():

    raceid = sys.argv[1]
    dbflag = sys.argv[2]
    swaptime = sys.argv[3]

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
    

    query = "SELECT * FROM raceresults WHERE raceid='%s'" % raceid
    racedb_cur.execute(query)


    results = racedb_cur.fetchall()

    '''
    Run through each of the new timestamps in the stamps TABLE
    '''
    for row in results:


        riderid = row['riderid']
        t1 = row['t1']
        t2 = row['t2']
        t3 = row['t3']
        t4 = row['t4']
        t5 = row['t5']
        t6 = row['t6']
        t7 = row['t7']
        t8 = row['t8']
        totaltime = row["totaltime"]


        ttotal,stages = calcTotalTime([t1,t2,t3,t4,t5,t6,t7,t8])

        total = calcSec(row["totaltime"])

        ttotal = ttotal + calcSec(row["penalty"])

        correction = (ttotal - total) if stages !=0 else 0

        s1 = timeStr(t1)
        s2 = timeStr(t2)
        s3 = timeStr(t3)
        s4 = timeStr(t4)
        s5 = timeStr(t5)
        s6 = timeStr(t6)
        s7 = timeStr(t7)
        s8 = timeStr(t8)

#        if total != 0:
#            t1 = t1 if (t1==0) else round(t1-correction,3)
#            t2 = t2 if (t2==0) else round(t2-correction,3)
#            t3 = t3 if (t3==0) else round(t3-correction,3)
#            t4 = t4 if (t4==0) else round(t4-correction,3)
#            t5 = t5 if (t5==0) else round(t5-correction,3)
#            t6 = t6 if (t6==0) else round(t6-correction,3)
#            t7 = t7 if (t7==0) else round(t7-correction,3)
#            t8 = t8 if (t8==0) else round(t8-correction,3)
#
#            ttotal,stages = calcTotalTime([t1,t2,t3,t4,t5,t6,t7,t8])
#
#            s1 = timeStr(t1)
#            s2 = timeStr(t2)
#            s3 = timeStr(t3)
#            s4 = timeStr(t4)
#            s5 = timeStr(t5)
#            s6 = timeStr(t6)
#            s7 = timeStr(t7)
#            s8 = timeStr(t8)
#
#            totaltime = "DNF" if row['place']==999 else timeStr(ttotal)


 


        #print "Total: %s   Stages: %s " % (ttotal,stages)

        if str(total) != str(ttotal):
            print "Updating results for Rider ",row["name"]
            print "WARNING: Total Time, %s, and Stage Total, %s, mismatch - Delta: %s" % (total,ttotal,correction)
        
        
        if swaptime == "swap" :

            totaltime = "DNF" if row['place']==999 else timeStr(ttotal)
            ttotal = 0 if row['place']==999 else ttotal

            query = ("UPDATE `raceresults` "
                     "SET `totaltime`=%s, `ttotal`=%s,`stages`=%s,"
                     "`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s, "
                     "`t1`=%s,`t2`=%s,`t3`=%s,`t4`=%s,`t5`=%s,`t6`=%s,`t7`=%s,`t8`=%s "
                     "WHERE `riderid`=%s AND `raceid`=%s ")
            data = (totaltime,ttotal,stages,s1,s2,s3,s4,s5,s6,s7,s8,t1,t2,t3,t4,t5,t6,t7,t8,riderid,raceid)

        else:

            query = ("UPDATE `raceresults` "
                     "SET `ttotal`=%s,`stages`=%s,"
                     "`t1`=%s,`t2`=%s,`t3`=%s,`t4`=%s,`t5`=%s,`t6`=%s,`t7`=%s,`t8`=%s "
                     "WHERE `riderid`=%s AND `raceid`=%s ")
            data = (ttotal,stages,t1,t2,t3,t4,t5,t6,t7,t8,riderid,raceid)


        racedb_cur.execute(query,data)  





   
if __name__ == "__main__":
    main();