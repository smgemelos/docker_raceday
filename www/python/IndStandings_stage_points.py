''' 
Usage

python IndStandingsdb.py <seriesid> <local/aws>

seriesid = 9 for 2019 CES
if using local DB, include local -- if using aws db include aws

'''

import os, math, sys, time
from datetime import datetime
#from dateutil import parser
import referencedb as ref
import pymysql

Category = {}
Standing = {}
Points = {}
Rider = {}
Races = {}

def PointsTotal(a,N):
    total = 0
    a.sort(reverse=True)
    for i in range(N):
        total = total + a[i]
    return total

def sort_lastrace(item,lastrace):
    index = 3+lastrace
    order = item[index]
    return order

def sort_points(item):
    order = item[3]
    return order

def sort_category(item):
    order = int( Category[item[0]]['sort'] )
    return order


def loadCategories(cur):
# Processing Categories File
    Dict = {}
    query = "SELECT * FROM category"
    cur.execute(query)

    for row in cur.fetchall():

        category = row['name'].encode('ascii')
        sortorder = int(row['sortorder'])
        cat = row['cat'].encode('ascii')
        gender = row['gender'].encode('ascii')
        minage = int(row['minage'])
        maxage = int(row['maxage'])
        dropraces = int(row['dropraces'])

        Dict[category] = {"sort":sortorder, "category":cat, "gender":gender, "minage":minage, "maxage":maxage, "dropraces":dropraces}

    return Dict


def loadRiders(cur):
    # Processing Riders File
    Dict = {}
    cur.execute("SELECT riderid FROM rider ")

    for row in cur.fetchall():

        riderid = row['riderid']
        ridername = row['name'] 
        print(riderid)
        print(row['name'].encode('ascii'))
        dob = row['dob']
        raceage = time.localtime()[0] - dob.year


        category = row['category']
        gender = row['gender']

        Dict[riderid] = {"ridername":ridername, "category":category, "gender":gender, "raceage":raceage}

    return Dict


def loadPoints(cur):
    # Processing Points Allocation File
    Dict = {}
    query = "SELECT * FROM points"
    cur.execute(query)

    for row in cur.fetchall():

        place = row['place'].encode('ascii')
        points = row['points']

        Dict[place] = int(points)

    return Dict


def loadRaces(cur,seriesid):
    # Processing Teams File
    Dict = {}
    query = "SELECT c.racenumber as racenum, a.*  FROM races a, seriesraces c WHERE c.seriesid='%s' AND a.id=c.raceid ORDER BY c.racenumber" % (seriesid)
    cur.execute(query)
    lastrace = 0

    for row in cur.fetchall():
        
        racenum = row['racenum']
        raceid = row['id']
        racename = row['name'].encode('ascii')
        racedate = row['racedate']
        lastrace = row['racenum']

        #racedate = time.strptime(row['racedate'].strip(), "%Y-%m-%d")
        
        Dict[racenum] = {"racename":racename, 'raceid':raceid, "date":racedate}

    return Dict,lastrace


def main():

    seriesid = sys.argv[1]
    dbflag = sys.argv[2]

    # Connect to the database

    if dbflag == "local":
        connection = pymysql.connect(host='localhost',
                                     user='sportident',
                                     password='sportident',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)

    else:
        connection = pymysql.connect(host='cesdb.californiaenduro.com',
                                     user='cesuser',
                                     password='wvG-Tkd-huo-72S',
                                     db='ces',
                                     charset='latin1',
                                     cursorclass=pymysql.cursors.DictCursor)

    connection.autocommit(True)
    cur = connection.cursor()

    cur = connection.cursor()
    query = "DELETE FROM seriesresults WHERE seriesid='%s'" % (seriesid)
    cur.execute(query)

    #query = "ALTER TABLE `seriesresults` AUTO_INCREMENT = 1"
    #cur.execute(query)


    #load_referencedata()
    global Rider, Category, Points

    Category = ref.loadCategories(cur)
    Rider = ref.loadRiders(cur)
    Points = ref.loadPoints(cur)
    Races,lastrace = ref.loadRaces(cur,seriesid)

    SeriesRaceCount = len(Races.keys())


    # Processing Results File 
    for l in Races.keys():

        racenumber = str(l)

        query = "SELECT queenstage FROM races WHERE id='%s'" % (str(Races[l]["raceid"]))
        cur.execute(query)
        queenstage = cur.fetchone()['queenstage']

        query = "SELECT * FROM raceresults WHERE raceid='%s'" % (str(Races[l]["raceid"]))
        cur.execute(query)

        for row in cur.fetchall():
            
            #racedate = time.strptime(row[1].strip(), "%m/%d/%y %H:%M")
            place = row['place']
            category = row['category'].strip()
            riderid = row['riderid'].strip()
            ridercat = riderid+category
            points = int(Points[str(place)])

            for i in ["1","2","3","4","5","6","7","8"]:
                si = "s" + i
                pi = "p" + i 
                if row[pi] == 1:
                    if si == queenstage:
                        points = points + 10
                    else:
                        #print "5 points"
                        points = points

            # Check for matching riderid
            if (riderid not in Rider):
                print("WARNING: Race ", l, " -  RiderID not found in DB: ", riderid," Reg Rider Name: ",row['name'])
                continue

            # Check for correct category
            #if (Rider[riderid]['category'] != Category[category]['category']):
            #    print("WARNING: Race ", l, " -  Race Category (",category,") does not match DB Category(",Rider[riderid]['category'],"): ", Rider[riderid]['ridername'])

            # Check for correct gender
            #if (Rider[riderid]['gender'] != Category[category]['gender']):
            #    print("WARNING: Race ", l, " -  Category Gender mismatch: ", Rider[riderid]['ridername'])
            #    continue

            # Check for correct raceage
            #minage = int(Category[category]['minage'])
            #maxage = int(Category[category]['maxage']) + 1
            #if ( Rider[riderid]['raceage'] not in range(minage,maxage) ):
            #    print("WARNING: Race ", l, " -  RaceAge (",Rider[riderid]['raceage'],") mismatch (",category,"): ", Rider[riderid]['ridername'])
            #    continue

            if (ridercat in Standing):
                Standing[ridercat][racenumber] = points
                if (row['stagewins'] == None ):
                    stagewins = 0
                else:
                    stagewins = row['stagewins']
                Standing[ridercat]["sw"+racenumber] = row['stagewins']
                print("ridercat ", ridercat, " -  racenumber", racenumber," stagewins: ",row['stagewins'])
                print("ridercat ", ridercat, " -  swtotal", Standing[ridercat]["swtotal"]," stagewins: ",stagewins)
                Standing[ridercat]["swtotal"] = Standing[ridercat]["swtotal"] + stagewins

            else:
                Standing[ridercat] = {"riderid":riderid, "ridername":Rider[riderid]["ridername"], "category":category, 
                                      "total":0, "1":0, "2":0 , "3":0 , "4":0 , "5":0 , "6":0, "7":0, "8":0, 
                                      "swtotal":0, "sw1":0, "sw2":0 , "sw3":0 , "sw4":0 , "sw5":0 , "sw6":0, "sw7":0, "sw8":0}
                Standing[ridercat][racenumber] = points
                Standing[ridercat]["sw"+racenumber] = row['stagewins']
                Standing[ridercat]["swtotal"] = row['stagewins']

    Results = []
    for k in Standing.keys():
        a = [0,0,0,0,0,0,0,0]
        for i in [1,2,3,4,5,6,7,8]:
            if Standing[k][str(i)] == "":
                a[i-1] = 0
            else:
                a[i-1] = Standing[k][str(i)]

        countraces = SeriesRaceCount - int( Category[Standing[k]["category"]]["dropraces"] )

        Standing[k]["total"] = PointsTotal(a,countraces)  
        Results.append([Standing[k]["category"],pymysql.converters.escape_string(Standing[k]["ridername"]),Standing[k]["riderid"],
                        Standing[k]["total"],Standing[k]["1"],Standing[k]["2"],
                        Standing[k]["3"],Standing[k]["4"],Standing[k]["5"],
                        Standing[k]["6"],Standing[k]["7"],Standing[k]["8"],
                        Standing[k]["swtotal"],Standing[k]["sw1"],Standing[k]["sw2"],
                        Standing[k]["sw3"],Standing[k]["sw4"],Standing[k]["sw5"],
                        Standing[k]["sw6"],Standing[k]["sw7"],Standing[k]["sw8"]  ])


    # Sorting by Category and Total Points
    # Results.sort(key=sort_lastrace, reverse=True)
    Results.sort(key=lambda r : r[3+lastrace], reverse=True)

    Results.sort(key=sort_points, reverse=True)
    Results.sort(key=sort_category)

    # Print the results 
    outputfile = open("Output/Ind_Results.csv","w")
    outputfile.write("Place, Category, RiderName, RiderID, Total, Race1, Race2, Race3, Race4, Race5, Race6, Race7, Race8\n")
    #print "Place, Category, RiderName, RiderID, Total, Race1, Race2, Race3, Race4, Race5, Race6, Race7"
    Place = 1
    PrevCat = Results[1][0]
    for i in Results:
        if PrevCat != i[0]:
            Place = 1
        outputfile.write("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n" % (Place,i[0],i[1],i[2],i[3],i[4],i[5],i[6],i[7],i[8],i[9],i[10],i[11]) )
        #print("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s" % (Place,i[0],i[1],i[2],i[3],i[4],i[5],i[6],i[7],i[8],i[9],i[10]) )
        
        raceresults = "{";
        for l in Races.keys():
            index = int(l)
            raceresults += '"Race ' + str(l) + '":' + str(i[index+3]) + ","
        raceresults += "}"

        query = "INSERT INTO `seriesresults` (`seriesid`,`category`,`name`,`riderid`,\
                             `total`,`place`,`raceresults`,`r1`,`r2`,`r3`,`r4`,`r5`,`r6`,`r7`,`r8`,\
                             `sw_total`,`sw_r1`,`sw_r2`,`sw_r3`,`sw_r4`,`sw_r5`,`sw_r6`,`sw_r7`,`sw_r8`\
                             ) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
        #print query
        data = (seriesid,i[0],i[1],i[2],i[3],Place,raceresults,
                i[4],i[5],i[6],i[7],i[8],i[9],i[10],i[11],
                i[12],i[13],i[14],i[15],i[16],i[17],i[18],i[19],i[20])

        cur.execute(query,data)

        PrevCat = i[0]
        Place = Place + 1

    outputfile.close()
    cur.close()
    connection.close()

   
if __name__ == "__main__":
    main();