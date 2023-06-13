''' 
This executable uses the reference data from the database, and the race results data
from the directory "RaceResults".  

The results are loaded to the database table "teamresults" and output to the file
"Output/Team_Results.csv"

Execution:

python TeamStandingsdb.py <seriesid> <local/aws>

seriesid = 9 for 2019 CES
if using local DB, include local -- if using aws db include aws


'''


import os, math, sys, time
from datetime import datetime
#from dateutil import parser
import referencedb as ref
import pymysql

TeamPoints = {}
Points = {}
Team = {}
TeamMember = {}
Rider = {}
Races = {}

def PointsTotal(a,N):
    total = 0
    a.sort(reverse=True)
    for i in range(N):
        total = total + a[i]
    return total


def get_sort_elements(iterable):
    order = int(iterable[2])
    return order


def main():

    seriesid = sys.argv[1]

    dbflag = sys.argv[2]

    #load_referencedata()
    global Team, TeamMember, Rider, Points

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
    query = "DELETE FROM `teamresults` WHERE series='%s'" % (str(seriesid))
    cur.execute(query)





    Team = ref.loadTeams(cur)
    TeamMember = ref.loadTeamMembers(cur)
    Rider = ref.loadRiders(cur)
    Points = ref.loadPoints(cur)
    Races,lastrace = ref.loadRaces(cur,seriesid)

    print(Races.keys())
    Results = []

    #Processing Results File
    for l in Races.keys():
        Standing = {}

        racenumber = str(l)
        query = "SELECT * FROM `raceresults` WHERE raceid='%s'" % (str(Races[l]["raceid"]))
        cur.execute(query)

        racedate = Races[l]['date'] 

        for row in cur.fetchall():
            
            place = row['place']
            riderid = row['riderid'].strip()
            

            if (riderid not in Rider):
                print("WARNING: Race ", l, " -  RiderID not found in DB: ", riderid," Reg Rider Name: ",row['name'])
                continue

            gender = Rider[riderid]['gender']
            
            if (riderid in TeamMember):
                if (racedate > TeamMember[riderid]['adddate'].date() ):

                    teamid = TeamMember[riderid]["teamid"]
                    if teamid in Standing:
                        Standing[teamid]['Points'].append(Points[str(place)])
                        Standing[teamid]['Female'] = Standing[teamid]['Female']+1 if gender == 'F' else Standing[teamid]['Female']
                    else:
                        Standing[teamid] = {}
                        Standing[teamid]['Points'] = [Points[str(place)]]
                        Standing[teamid]['Female'] = 1 if gender == 'F' else 0

                else:
                    print("WARNING: Race ", l, " - Rider Skipped based on date added to team: ", Rider[riderid]['ridername'])

        #print(Standing)
        
        Results = []
        for k in Team.keys():
            if k in Standing:          
                if ((len(Standing[k]['Points']) >=3) and (Standing[k]['Female'] >= 1)):
                    racetotal = PointsTotal(Standing[k]['Points'],3)
                    Team[k][racenumber] = racetotal
                    Team[k]["total"] = Team[k]["total"] + racetotal
                else:
                    Team[k][racenumber] = 0
            else:
                Team[k][racenumber] = 0
            Results.append([ Team[k]["teamname"],k,Team[k]["total"],
                             Team[k]["1"],Team[k]["2"],Team[k]["3"],
                             Team[k]["4"],Team[k]["5"],Team[k]["6"],
                             Team[k]["7"],Team[k]["8"] ])

    # Sorting by Category and Total Points
    Results.sort(key=lambda r : r[1+lastrace], reverse=True)
    Results.sort(key=get_sort_elements, reverse=True)

    outputfile = open("Output/Team_Results.csv","w")
    outputfile.write("Place, Team Name, Total, Race1, Race2, Race3, Race4, Race5, Race6, Race7, Race8\n")

    Place = 0
    Total = 0
    Index = 0
    for i in Results:

        if i[2] == Total:
            Index += 1
        else:
            Place = Place + 1 + Index
            Index = 0
            Total = i[2]

        outputfile.write("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n" % (Place,i[0],i[2],i[3],i[4],i[5],i[6],i[7],i[8],i[9],i[10]) )

        #raceresults = "{";
        #for l in Races.keys():
        #    index = int(l)
        #    raceresults += '"' + str(l) + '":' + str(i[index+2]) + ","
        #raceresults += "}"
        raceresults = ""

        query = "INSERT INTO `teamresults` (`series`,`teamname`,`teamid`,`place`,`total`,`raceresults`,`r1`,`r2`,`r3`,`r4`,`r5`,`r6`,`r7`,`r8`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"  
        #print query
        data = (seriesid,i[0],i[1],Place,i[2],raceresults,i[3],i[4],i[5],i[6],i[7],i[8],i[9],i[10])
        cur.execute(query,data)

        

    outputfile.close()
    cur.close()
    connection.close()


   
if __name__ == "__main__":
    main();