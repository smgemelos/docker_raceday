''' 
This executable is used to load race results into the DB table "raceresults"

Execution:

python3 loadresultsDB_StagePlacement_StageWins.py 20239 aws


'''


import os, sys, math, time
import pymysql
import datetime as d


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
            print("check this time %s" % s)

        #print "sec: %s  msec: %s" % (sec,millisec)

        time = "%s.%s" % (sec,millisec)

        return float(time)
    
    except:
        return 0.0


def main():


    print("")
    print("Uploading Results Data...")

    # Connect to the database

    raceid = sys.argv[1]
    dbflag = sys.argv[2]

    if dbflag == "local":
        cesdb = pymysql.connect(host='localhost',
                                     user='sportident',
                                     password='sportident',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)

    else:
        cesdb = pymysql.connect(host='cesdb.californiaenduro.com',
                                     user='cesuser',
                                     password='wvG-Tkd-huo-72S',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)
    
    cesdb.autocommit(True)
    cesdb_cur = cesdb.cursor()




    racedb = pymysql.connect(host='localhost',
                             user='sportident',
                             password='sportident',
                             db='lcsportident_events',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    racedb.autocommit(True)
    racedb_cur = racedb.cursor()

    query = "DELETE FROM raceresults WHERE raceid='%s'" % (raceid)
    cesdb_cur.execute(query)

    catStages = {}
    maxStages = 0
    Stages = ['s1','s2','s3','s4','s5','s6','s7','s8','s9','s10','s11','s12']

    query = "SELECT * FROM categories WHERE raceid='%s' ORDER BY sortorder DESC " % (raceid)
    cesdb_cur.execute(query)

    for row in cesdb_cur.fetchall():
            
        name = row['name']
        stages = row['stages']
        if (stages > maxStages):
            maxStages = stages
        catStages[name] = stages

    query = "SELECT a.name, a.plate, a.riderid, a.category, b.sortorder, a.ranktotal, a.penalty, a.total, a.ttotal, a.stages, \
                    a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8, a.s9, a.s10, a.s11, a.s12 \
             FROM raceresults a, categories b \
             WHERE a.category=b.name \
             ORDER BY a.stages DESC, b.sortorder, a.ttotal  "
    racedb_cur.execute(query)


    place = 0
    currentcat = "PRO MEN"

    for row in racedb_cur.fetchall():
        #print(line)

        #raceid = row['raceid']
        riderid = pymysql.converters.escape_string(row['riderid'])
        category = row['category']
        name = pymysql.converters.escape_string(row['name'])
        totaltime = row['total'] 
        penalty = '' if (row['penalty']=='None') else row['penalty']
        plate = row['plate']
        s1 = row['s1']
        s2 = row['s2']
        s3 = row['s3']
        s4 = row['s4']
        s5 = row['s5']
        s6 = row['s6']
        s7 = row['s7']
        s8 = row['s8']
        s9 = row['s9']
        s10 = row['s10']
        s11 = row['s11']
        s12 = row['s12']

        t1 = calcSec(s1)
        #print "stage 1: " + s1 + "  " + t1
        t2 = calcSec(s2)
        #print "stage 2: " + s2 + "  " + t2
        t3 = calcSec(s3)
        #print "stage 3: " + s3 + "  " + t3
        t4 = calcSec(s4)
        #print "stage 4: " + s4 + "  " + t4
        t5 = calcSec(s5)
        #print "stage 5: " + s5 + "  " + t5
        t6 = calcSec(s6)
        #print "stage 6: " + s6 + "  " + t6
        t7 = calcSec(s7)
        #print "stage 7: " + s7 + "  " + t7
        t8 = calcSec(s8)
        t9 = calcSec(s9)
        t10 = calcSec(s10)
        t11 = calcSec(s11)
        t12 = calcSec(s12)
        #print "stage 8: " + s8 + "  " + t8
        ttotal = calcSec(totaltime)


        stagescompleted = 0
        for stage in Stages:
            if (row[stage] != ""):
                stagescompleted += 1            
        

        if ( stagescompleted < catStages[row['category']] ) :
            total = "DNF"
            ttotal = 0
        else :
            total = row['total']
        

        if (category == currentcat) :
            place = place + 1
        else :
            place = 1;
            currentcat = category

        if ( total == "DNF" ) :
            placestr = 999
        else :
            placestr = place

        #print "Category: %s,  Place: %s, Total: %s" % (category,placestr,total)
        

        query = "INSERT INTO raceresults \
                    (plate,raceid,riderid,category,name,place,totaltime,ttotal,stages,penalty,\
                     s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12,t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,t11,t12) \
                 VALUES \
                    (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 

        data = (plate,raceid,riderid,category,name,placestr,total,ttotal,stagescompleted,penalty,s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12,t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,t11,t12)
 
        cesdb_cur.execute(query,data)



    print("")
    print("Adding Stage Placement....")


    for i in ['1','2','3','4','5','6','7','8','9','10','11','12']:

        tval = 't'+i
        pval = 'p'+i
    

        query = "SELECT * FROM raceresults WHERE raceid='%s' AND %s > 0 ORDER BY category, %s" % (raceid,tval,tval)
        cesdb_cur.execute(query)


        results = cesdb_cur.fetchall()

        cat = ""
        place = 0
        prevtime = 0

        for row in results:


            if row['category'] != cat:
                cat = row['category']
                place = 1
                prevtime = 0
            else:
                if row[tval] == prevtime:
                    place = place

                else:
                    place = place + 1

            
            query = "UPDATE raceresults SET "+pval+"=%s WHERE riderid=%s AND raceid=%s " 
            data = (place,row['riderid'],raceid)

            cesdb_cur.execute(query,data)  




    print("")
    print("Calculating Stage Wins....")


    query = "SELECT * FROM raceresults WHERE raceid='%s'" % raceid 
    cesdb_cur.execute(query)


    results = cesdb_cur.fetchall()

    '''
    Run through each of the new timestamps in the stamps TABLE
    '''
    for row in results:

        stagewins = 0
        for i in range(1,12):
            if (row['p'+str(i)] == 1):
                stagewins += 1

        query = "UPDATE `raceresults` SET `stagewins`=%s WHERE `id`=%s"
        data = (stagewins,row['id'])


        cesdb_cur.execute(query,data)  





    print("")
    print("DONE - Click Home to go back.")


    cesdb_cur.close()
    cesdb.close()
    racedb_cur.close()
    racedb.close()
    

   
if __name__ == "__main__":
    main();