
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

    print "Starting"


    #load_referencedata()
    global Team, TeamMember, Rider, Points

    # Connect to the database

    connection = pymysql.connect(host='localhost',
                             user='root',
                             password='root',
                             db='lcsportident_events',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)

    connection.autocommit(True)

    cur = connection.cursor()
    query = "DELETE FROM `teamresults` "
    cur.execute(query)


    Team = ref.loadTeams(cur)
    TeamMember = ref.loadTeamMembers(cur)
    Points = ref.loadPoints(cur)



    catStages = {}
    maxStages = 0
    Stages = ['s1','s2','s3','s4','s5','s6','s7','s8']

    query = "SELECT * FROM categories ORDER BY sortorder DESC "

    cur.execute(query)

    for row in cur.fetchall():
            
        name = row['name']
        stages = row['stages']
        if (stages > maxStages):
            maxStages = stages
        catStages[name] = stages

    query = "SELECT a.name, a.riderid, a.category, b.sortorder, a.ranktotal, a.penalty, a.total, a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8 FROM `raceresults` a, `categories` b WHERE a.category=b.name ORDER BY b.sortorder, a.ranktotal "
    cur.execute(query)


    place = 0
    currentcat = "PRO MEN"

    for row in cur.fetchall():
        #print(line)

        #raceid = row['raceid']
        riderid = pymysql.escape_string(row['riderid'])
        category = row['category']
        name = pymysql.escape_string(row['name'])
        totaltime = row['total']
        penalty = row['penalty']
        s = [ row['s1'],row['s2'],row['s3'],row['s4'],row['s5'],row['s6'],row['s7'],row['s8'] ]


        stagescompleted = 0
        for stage in Stages:
            if (row[stage] != ""):
                stagescompleted += 1            
        

        if ( stagescompleted < catStages[row['category']] ) :
            total = "DNF"
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
        

        query = "UPDATE raceresults SET place='%s' WHERE riderid='%s'" %  (placestr,riderid)
        cur.execute(query)









    #Processing Results File

    Standing = {}

    query = "SELECT * FROM `raceresults` " 
    cur.execute(query)

    today = datetime.today()
    racedate = datetime(today.year, today.month, today.day)

    for row in cur.fetchall():
        
        place = row['place']
        riderid = row['riderid'].strip()

        if (riderid in TeamMember):

            teamid = TeamMember[riderid]["teamid"]
            if teamid in Standing:
                Standing[teamid].append(Points[str(place)])
            else:
                Standing[teamid] = [Points[str(place)]]


    #print(Standing)
    
    Results = []
    for k in Team.keys():

        if k in Standing:       
            if len(Standing[k]) >=3:
                racetotal = PointsTotal(Standing[k],3)
                Team[k]["total"] = racetotal
            else:
                Team[k]["total"] = 0
        else:
            Team[k]["total"] = 0

        Results.append([ Team[k]["teamname"], k, Team[k]["total"] ])



    # Sorting by Category and Total Points
    # Results.sort(key=lambda r : r[1+lastrace], reverse=True)
    Results.sort(key=get_sort_elements, reverse=True)

    Place = 0
    for i in Results:

        Place = Place + 1 
            

        query = "INSERT INTO `teamresults` (`teamname`,`teamid`,`place`,`total`) VALUES (%s,%s,%s,%s)"  
        data = (i[0],i[1],Place,i[2])
        cur.execute(query,data)

        

    outputfile.close()
    cur.close()
    connection.close()


   
if __name__ == "__main__":
    main();