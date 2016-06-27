# digital-eye
Van den Borne: een camera systeem om foto's van gewassen te maken op willekeurige locaties

https://farmhacknl.github.io/digital-eye/WebContent/

**The farmer's problem**
- has a lot of data (maps, satellite/drone images, sensors) but lacks *visual* information about his/her crops
- collecting visual information (photographs) is a manual - and therefore error prone - process

**Our solution**: automate the picture taking process by
- attaching a smart camera to a farm vehicle
- building an app/platfrom that 1) allows the farmer to pinpoint locations that he/she wants photographed and 2) makes the camera take pictures when the machine passes these locations
- collecting all photographs at a central location combined with additional information such as date, plot ID, time, crop, weathers, etc.

Find the full idea on [Google Drive](https://docs.google.com/document/d/12SfnumPdTdqToLzTXJ9ppxzw8s2wOXEPjD0vakotmFY/edit#) (Dutch).

Part 1. Simulate Photograph Creation 
This HTML file combined with Javascript creates a Polygon at given GEOLocations.
A marker is placed at the edge of the Polygon and moves slowly in a given direction.
Within the Polygon a user can place a Point Of Interest which is represented by a circle.
If the marker crosses this circle it will write a quick alert which says it has taken a photograph.

Part 2. Simulate Photograph Usage
The Html file contains simple GUI options to select photo's from photo archive (called north and south). 
A route can be selected on which dots with photographs are displayed green.
The user can select such a dot and browse throught the photographs (enlarged by clicking on the photograph)    
Via arrow keys the user can direct the car inside the selected landscape. 

Note: this project has won the first price during the first official FarmHack hackaton in NL!
More information on this hackaton can be found here: http://www.farmhack.nl/challenge/datavisualisatie-bij-een-pieperboer/
