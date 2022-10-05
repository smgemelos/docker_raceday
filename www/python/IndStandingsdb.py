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


def main():

    seriesid = sys.argv[1]
    dbflag = sys.argv[2]

    # Connect to the database

    if dbflag == "local":
        connection = pymysql.connect(host='localhost',
                                     user='root',
                                     password='root',
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

    Rider = ref.loadRiders(cur)
    Category = ref.loadCategories(cur)
    Points = ref.loadPoints(cur)
    Races,lastrace = ref.loadRaces(cur,seriesid)


    # Processing Results File 
    for l in Races.keys():

        racenumber = str(l)

        query = "SELECT * FROM raceresults WHERE raceid='%s'" % (str(Races[l]["raceid"]))
        cur.execute(query)

        for row in cur.fetchall():
            
            #racedate = time.strptime(row[1].strip(), "%m/%d/%y %H:%M")
            place = row['place']
            category = row['category'].strip()
            riderid = row['riderid'].strip()
            ridercat = riderid+category
            points = int(Points[str(place)])


            # Check for matching riderid
            if (riderid not in Rider):
                print "WARNING: Race ", l, " -  RiderID not found in DB: ", riderid," Reg Rider Name: ",row['name']
                continue

            # Check for correct category
            #if (Rider[riderid]['category'] != Category[category]['category']):
            #    print "WARNING: Race ", l, " -  Race Category (",category,") does not match DB Category(",Rider[riderid]['category'],"): ", Rider[riderid]['ridername']

            # Check for correct gender
            #if (Rider[riderid]['gender'] != Category[category]['gender']):
            #    print "WARNING: Race ", l, " -  Category Gender mismatch: ", Rider[riderid]['ridername']
            #    continue

            # Check for correct raceage
            #minage = int(Category[category]['minage'])
            #maxage = int(Category[category]['maxage']) + 1
            #if ( Rider[riderid]['raceage'] not in range(minage,maxage) ):
            #    print "WARNING: Race ", l, " -  RaceAge (",Rider[riderid]['raceage'],") mismatch (",category,"): ", Rider[riderid]['ridername']
            #    continue

            if (ridercat in Standing):
                Standing[ridercat][racenumber] = points
            else:
                Standing[ridercat] = {"riderid":riderid, "ridername":Rider[riderid]["ridername"], "category":category, 
                                      "total":0, "1":0, "2":0 , "3":0 , "4":0 , "5":0 , "6":0, "7":0, "8":0}
                Standing[ridercat][racenumber] = points

                

    Results = []
    for k in Standing.keys():
        a = [0,0,0,0,0,0,0,0]
        for i in [1,2,3,4,5,6,7,8]:
            if Standing[k][str(i)] == "":
                a[i-1] = 0
            else:
                a[i-1] = Standing[k][str(i)]

        countraces = 6 - int( Category[Standing[k]["category"]]["dropraces"] )

        Standing[k]["total"] = PointsTotal(a,countraces)  
        Results.append([Standing[k]["category"],pymysql.escape_string(Standing[k]["ridername"]),Standing[k]["riderid"],
                        Standing[k]["total"],Standing[k]["1"],Standing[k]["2"],
                        Standing[k]["3"],Standing[k]["4"],Standing[k]["5"],
                        Standing[k]["6"],Standing[k]["7"],Standing[k]["8"] ])


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

        query = "INSERT INTO `seriesresults` (`seriesid`,`category`,`name`,`riderid`,`total`,`place`,`raceresults`,`r1`,`r2`,`r3`,`r4`,`r5`,`r6`,`r7`,`r8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
        #print query
        data = (seriesid,i[0],i[1],i[2],i[3],Place,raceresults,i[4],i[5],i[6],i[7],i[8],i[9],i[10],i[11])
        cur.execute(query,data)

        PrevCat = i[0]
        Place = Place + 1

    outputfile.close()
    cur.close()
    connection.close()

   
if __name__ == "__main__":
    main();