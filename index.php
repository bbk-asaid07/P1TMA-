<?php

include 'functions.php';
$filenames = array();
$format  = ".txt"; // looking for files with extension ".txt" only
$mm = "Data";

	// reading file names
foreach (glob("$mm/*") as $file) {
	array_push($filenames, $file);
}

// starting to create dynamic html file
echo "<html>";
echo "<head>";
echo "<title> Student Results </title>";
echo "</head>";
echo "<body>";



// loop on number of files found in the directory

for($i=0;$i<count($filenames);$i++){
	
	
	$includedarr = array(); // array to get students and marks included
	$marksarr = array(); // array to store marks of students
	$numofstudents = 0; //variable to store number of students;
	$headererror = 0; //variable to store number of header errors;
	$studentdataerror = 0; //variable to store number of student data errors;
	
	//variables for merit
	$dist = 0;
	$merit = 0;
	$pass = 0;
	$fail = 0;
	
	$ext = pathinfo($filenames[$i], PATHINFO_EXTENSION); // checking file extension if it txt or not
	$tempname = substr($filenames[$i], strpos($filenames[$i], "/") + 1); // getting file naame from directory+file name e.g. Data/name.txt
	if( $ext != "txt"){
		
		echo "<p>". $tempname .": INVALID FILE EXTENSION- should be .txt</p><br>";   
	}else{
		echo "<p><b>Module Header Data...</b></p>"; 
		echo "<p>File name : ".$tempname."</p>"; 
		
		$handle = fopen($filenames[$i], "r"); // open file now
		$numofrow = 0;
		if ($handle) {
			while (($line = fgets($handle)) !== false) { //reading file line by line
				if($numofrow == 0){// this means that this is first line i.e. header
					$headerline = explode(",", $line); // breaking first header line into array 
					
					// MODULE CODE PART ****************************************************************************
					if($headerline[0]==""){ // if no code is given
						echo "<p>Module Code : : ERROR</p>"; 
					}else{
						if(!(substr($headerline[0],0,2) == "PP" || substr($headerline[0],0,2) == "P1" || substr($headerline[0],0,2) == "DT")){
							//if 2 char code are not PP, P1 or DT
							echo "<p>Module Code : ".$headerline[0]." : ERROR</p>";
							$headererror+=1;
						}else{
							//module code is fine
							if(!(substr($headerline[0],6) == "T1" || substr($headerline[0],6) == "T2" || substr($headerline[0],6) == "T3")){
								echo "<p>Module Code : ".$headerline[0]." : ERROR</p>";
								$headererror+=1;
							}else{
								echo "<p>Module Code : ".$headerline[0]."</p>";
							}
						}
					}
					
					// MODULE TITLE ****************************************************************************
					if($headerline[1]==""){ // if no title is given
						echo "<p>Module Title : : ERROR</p>"; 
						$headererror+=1;
					}elseif(strlen(trim($headerline[1])) == 0){
						echo "<p>Module Title : : ERROR</p>"; 
						$headererror+=1;
					}else{
						echo "<p>Module Title : ".$headerline[1]."</p>";
					}
					
					// MODULE TITLE ****************************************************************************
					if($headerline[2]==""){ // if no TUTOR is given
						echo "<p>Tutor : : ERROR</p>"; 
						$headererror+=1;
					}elseif(strlen(trim($headerline[2])) == 0){
						// tutor has only spaces
						echo "<p>Tutor : : ERROR</p>"; 
						$headererror+=1;
					}
					else{
						echo "<p>Tutor  : ".$headerline[2]."</p>";
					}
					
					// MARK DATE CODE ****************************************************************************
					$testdate = explode('/',$headerline[3]);
					
					if(count($testdate) == 3){
						if(checkdate(intval($testdate[1]), intval($testdate[0]), intval($testdate[2]))){
							//date is validated and is fine
							echo "<p>Marked date : ". $headerline[3] . "</p>"; 
						}else{
							echo "<p>Marked date : ". $headerline[3] . ": ERROR</p>"; 
							$headererror+=1;
						}
					}else{
						// date is not in correct format
						echo "<p>Marked date : ". $headerline[3] . ": ERROR</p>"; 
						$headererror+=1;
					}
					
					/*if (!preg_match('/(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/',$headerline[3])) {
						// if date doesn`t match the regex for date type  DD/MM/YYYY it will show error
						echo "<p>Marked date : ". $headerline[3] . ": ERROR</p>"; 
						$headererror+=1;
					} else {
						echo "<p>Marked date : ". $headerline[3] . "</p>"; 
					}*/
					
					
					
					
					echo "<p><b>Student ID and Mark data read from file...</b></p>"; // write this tag after header file
				}else{ // these are remaining lines.. other than headers.
					
					$otherlines = explode(",", $line); // breaking other line into array
					
					
					// CHECKING STUDENT ID ****************************************************************************
					if(!is_numeric($otherlines[0])){
						// if ID is not numeric
						echo "<p>".$otherlines[0]." : ".$otherlines[1]." - Incorrect student ID : not included</p>";
						$studentdataerror+=1;
					}elseif(strlen($otherlines[0])<8){
						// if length of ID is less than 8
						echo "<p>".$otherlines[0]." : ".$otherlines[1]." - Incorrect student ID : not included</p>";
						$studentdataerror+=1;
					}else{
						// CHECKING STUDENT MARKS ****************************************************************************
						$marks = (int)$otherlines[1];
						if($marks < 0 || $marks > 100){
							// if marks are less than zero or greater than 100
							echo "<p>".$otherlines[0]." : ".$otherlines[1]." - Incorrect mark : not included</p>";
							$studentdataerror+=1;
						}else{
							echo "<p>".$otherlines[0]." : ".$otherlines[1]." </p>";
							$topush = $otherlines[0]." : ".$otherlines[1];
							array_push($includedarr ,$topush);
							array_push($marksarr,$marks);
							$numofstudents+=1;
							
							// calculating Distribution of module marks...
							if($marks>=70){
								$dist+=1;
							}elseif($marks>=60 && $marks <70){
								$merit+=1;
							}elseif($marks>=40 && $marks <= 59){
								$pass += 1;
							}else{
								$fail +=1;
							}
						}
					}
					
					
					
					
					
					
				}
				$numofrow+=1;
				//echo $line."<br>";
			}
			
			echo "<p><b>ID's and module marks to be included...</b></p>"; // writing next tag
				for($x=0;$x<count($includedarr);$x++){
					echo "<p>".$includedarr[$x]."</p>";
				}	
					
			echo "<p><b>Statistical Analysis of module marks...</b></p>"; // writing next tag
			
			echo "<p>Mean: ".mmmr($marksarr,'mean')."</p>";
			echo "<p>Mode: ".mmmr($marksarr,'mode')."</p>";
			echo "<p>Range: ".mmmr($marksarr,'range')."</p>";
			
			
			echo "<p># of students:".$numofstudents."</p>"; // writing number of students
			echo "<p># of Header Errors:".$headererror."</p>"; // writing number of Header data errors
			echo "<p># of Student data Errors:".$studentdataerror."</p>"; // writing number of Header data errors
			
			echo "<p><b>Grade Distribution of module marks...</b></p>"; // writing next tag
			
			
			//*********************** displaying grades distributions
			
			echo "<p>Dist:".$dist."</p>"; // Distinctions
			echo "<p>Merit:".$merit."</p>"; // Distinctions
			echo "<p>Pass:".$pass."</p>"; // Distinctions
			echo "<p>Fail:".$fail."</p>"; // Distinctions
			
			fclose($handle);
		}
	
	}
	
	
	echo "-----------------------------------------------------------------------------------------"; // draw line after each file read

}

echo "</body>";
echo "</html>";
?>