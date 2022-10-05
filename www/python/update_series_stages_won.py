import os, math, sys, time
import datetime as d
import referencedb as ref
#from dateutil import parser
import pymysql

Category = {}
StageWins = {}

def sort_points(item):
    order = item[3]
    return order

def sort_category(item):
    order = int( Category[item[0]]['sort'] )
    return order


def main():

    

    seriesid = sys.argv[1]
    dbflag = sys.argv[2]

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

    global StageWins, Category
    Category = ref.loadCategories(racedb_cur)

    query = "DELETE FROM `seriesstagewins` WHERE seriesid='%s'" % (str(seriesid))
    racedb_cur.execute(query)

    query = "SELECT c.name, c.category, c.riderid, c.stagewins, e.racenumber, d.sortorder \
             FROM raceresults c, category d, races e  \
             WHERE c.raceid=e.id AND c.category=d.name AND c.raceid IN \
                  (SELECT a.id \
                   FROM races a, seriesraces b \
                   WHERE b.seriesid='%s' AND a.id=b.raceid ORDER BY a.racenumber) \
             ORDER BY d.sortorder, e.racenumber, c.stagewins DESC" % seriesid

    racedb_cur.execute(query)


    results = racedb_cur.fetchall()

    '''
    Run through each of the new timestamps in the stamps TABLE
    '''
    for row in results:

        ridercat = row['riderid']+row['category']

        if (ridercat in StageWins):
            StageWins[ridercat]["r"+str(row['racenumber'])] = row['stagewins']
            StageWins[ridercat]["total"] = StageWins[ridercat]["total"] + row['stagewins']

        else:
            StageWins[ridercat] = {
                "name":row['name'],
                "category":row['category'],
                "riderid":row['riderid'],
                "total":0,
                "r1":0,
                "r2":0,
                "r3":0,
                "r4":0,
                "r5":0,
                "r6":0,
                "r7":0,
                "r8":0
            };
            StageWins[ridercat]["r"+str(row['racenumber'])] = row['stagewins']
            StageWins[ridercat]["total"] = row['stagewins']

    Results = []
    for k in StageWins.keys(): 
        Results.append([StageWins[k]["category"],pymysql.escape_string(StageWins[k]["name"]),StageWins[k]["riderid"],
                        StageWins[k]["total"],StageWins[k]["r1"],StageWins[k]["r2"],
                        StageWins[k]["r3"],StageWins[k]["r4"],StageWins[k]["r5"],
                        StageWins[k]["r6"],StageWins[k]["r7"],StageWins[k]["r8"] ])


    Results.sort(key=sort_points, reverse=True)
    Results.sort(key=sort_category)

    for i in Results:

        if (i[3]>0):
            query = "INSERT INTO `seriesstagewins` (`seriesid`,`category`,`name`,`riderid`,`total`,`r1`,`r2`,`r3`,`r4`,`r5`,`r6`,`r7`,`r8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
            #print query
            data = (seriesid,i[0],i[1],i[2],i[3],i[4],i[5],i[6],i[7],i[8],i[9],i[10],i[11])
            racedb_cur.execute(query,data)

   
if __name__ == "__main__":
    main();