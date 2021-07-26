<?php

function getSubjectArea($c){
    $course_subject_area = [
        'AFAM' => 'African American Studies',
        'ANTH' => 'Anthropology',
        'ARTH' => 'Art History',
        'ARTS' => 'Arts',
        'ASIA' => 'Asian Studies',
        'BAS'  => 'B.A. Seminar',
        'BIO'  => 'Biology',
        'CHEM' => 'Chemistry',
        'CHIN' => 'World Languages and Cultures - Chinese',
        'CMPT' => 'Computer Science',
        'COM'  => 'Communications',
        'DANC' => 'Dance',
        'ECON' => 'Economics',
        'ENVS' => 'Environmental Studies',
        'ESL'  => 'World Languages and Cultures - English',
        'FILM' => 'Film',
        'FREN' => 'World Languages and Cultures - French',
        'FS'   => 'Seminar',
        'GEOG' => 'Geography',
        'GERM' => 'World Languages and Cultures - German',
        'GS'   => 'Gender Studies',
        'HIST' => 'History',
        'LATN' => 'World Languages and Cultures - Latin',
        'LING' => 'Linguistics',
        'LIT'  => 'Literature',
        'LR'   => 'Learning Resources',
        'MATH' => 'Mathematics',
        'MUS'  => 'Music',
        'NATS' => 'Natural Sciences',
        'PACE' => 'Pathway to Academic Choice',
        'PHIL' => 'Philosophy',
        'PHOT' => 'Photography',
        'PHYS' => 'Physics',
        'POLS' => 'Political Science',
        'PSYC' => 'Psychology',
        'SART' => 'Studio Arts',
        'SOCS' => 'Social Science',
        'SOC'  => 'Sociology',
        'SPAN' => 'World Languages and Cultures - Spanish',
        'THEA' => 'Theater',
        'WS'   => 'Women Studies'
    ];
    return $course_subject_area[$c];
}

function s($val){
    $r = str_replace("'", "\'", $val);
    $r = str_replace('"', '\"', $r);
    return '"'.$r.'"';
}

function debug_queries($queries, $title){
    echo "<h1>$title</h1>";
    foreach ($queries as $q){
        echo $q."<br />";
    }
}

class Course{

    public $id;
    public $num;
    public $title;
    public $credits;
    public $desc;
    public $fee;
    public $prereq = Array(); // List
    public $coreq = Array(); // List
    public $sections = Array();

    function getLevel(){
        $e = explode(" ", $this->num);
        return floor(intval($e[1])/100)*100;
    }

    function getCourseId($conn){
        $query = "SELECT MAX(id) as MAXID FROM course";
        try {
            $result = $conn->query($query);
            foreach ($result as $row){
                return intval($row['MAXID'])+1;
            }
        }catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    function getDescId($conn){
        $query = "SELECT MAX(id) as MD FROM description";
        try {
            $result = $conn->query($query);
            foreach ($result as $row) {
                return intval($row['MD'])+1;
            }
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    function getCourseIDFromDB($course, $comparator, $conn){
        $query = "SELECT * FROM course WHERE $comparator = '".$course."'";

        try{
            $result = $conn->query($query);
            foreach( $result as $row ){
                return $row['id'];
            }
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }

        return -1;
    }

    function getSameCourses($conn){
        $a = Array();
        $query = "SELECT id FROM course WHERE title =".s($this->title);

        try{
            $result = $conn->query($query);
            foreach( $result as $row ){
                array_push($a, $row['id']);
            }
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }

        return $a;
    }

    function getSubjectArea($conn){
        $e = explode(" ", $this->num)[0];
        $area = getSubjectArea($e);
        $query = "SELECT id FROM subject_area WHERE name =".s($area);
        try{
            $results = $conn->query($query);
            foreach($results as $row){
                return $row['id'];
            }
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    function populateFromDB($conn, $id){
        $this->id = $id;

        $query = "SELECT * FROM course WHERE id=$id";
        $results = $conn->query($query);
        foreach($results as $row){
            $this->num =  $row['num'];
            $this->title = $row['title'];
            $this->credits = $row['credits'];
        }

        $query = "SELECT * FROM description INNER JOIN course_description ON  description.id=course_description.description_id WHERE course_id = $id";
        $results = $conn->query($query);
        foreach($results as $row){
            $this->desc = $row['descript'];
        }

        $this->fee = "None";
        $query = "SELECT Fee_Type FROM course_fee WHERE course_id = $id";
        $results = $conn->query($query);
        foreach($results as $row){
            $this->fee = $row['Fee_Type'];
        }

        $query = "SELECT * FROM course_to_course_relationship INNER JOIN course ON course_to_course_relationship.course1_id=course.id WHERE course_predicate_id = 1 AND course2_id = $id";
        $results = $conn->query($query);
        foreach ($results as $row){
            array_push($this->prereq, $row['num']);
        }

        $query = "SELECT * FROM course_to_course_relationship INNER JOIN course ON course_to_course_relationship.course1_id=course.id WHERE course_predicate_id = 2 AND course2_id = $id";
        $results = $conn->query($query);
        foreach ($results as $row){
            array_push($this->coreq, $row['num']);
        }

        $query = "SELECT * FROM section WHERE course_id = $id";
        $results = $conn->query($query);
        foreach ($results as $row){
            $section = new Section();
            $section->populate($conn, $row['course_id'], $row['letter'], $row['semester']);
            array_push($this->sections, $section);
        }
    }

    function deleteFromDB($conn){
        $queries = [];
        $id = $this->id;
        
        $query = "DELETE FROM course_description WHERE course_id=$id";
        array_push($queries, $query);

        $dq = "SELECT description_id FROM course_description WHERE course_id = $id";
        $res = $conn->query($dq);
        foreach ($res as $row){
            $d = $row['description_id'];
            $query = "DELETE FROM description WHERE id=$d";
            array_push($queries, $query);
        }

        
        
        $query = "DELETE FROM course_fee WHERE course_id=$id";
        array_push($queries, $query);

        $query = "DELETE FROM course_to_course_relationship WHERE course1_id = $id OR course2_id = $id";
        array_push($queries, $query);

        $query = "DELETE FROM course_subject_area WHERE course_id = $id";
        array_push($queries, $query);

        foreach ($this->sections as $s){
            $s->delete($conn);
        }

        debug_queries($queries, "DELETE COURSE ".$this->num.": ".$this->title);

        try{
            $conn->beginTransaction();
            foreach(array_reverse($queries) as $q){
                $conn->exec($q);
            }
            $query = "DELETE FROM course WHERE id=$id";
            $conn->exec($query);
            $conn->commit();
        } catch (PDOException $e){
            echo $e->getMessage();
            //exit();
        }
    }

    function finalDelete($conn){
        $this->deleteFromDB($conn);
    }

    function update($conn){
        $this->deleteFromDB($conn);
        $this->addToDB($conn);
    }

    function addToDB($conn){
        $queries = Array();
        try{
            $id = $this->id;
            $query = "INSERT INTO course VALUES (".$id.",".s($this->num).",".s($this->title).",".$this->credits.",".$this->getLevel().')';
            array_push($queries, $query);

            $desc_id = $this->getDescId($conn);
            $query = "INSERT INTO description VALUES (".$desc_id.",".s($this->desc).")";
            array_push($queries, $query);

            $query = "INSERT INTO course_description VALUES($id, $desc_id)";
            array_push($queries, $query);

            if($this->fee != "None"){
                $query = "INSERT INTO course_fee VALUES($id,".s($this->fee).")";
                array_push($queries, $query);
            }

            if(count($this->prereq) >= 1){
                foreach( $this->prereq as $p){
                    $pid = $this->getCourseIDFromDB(trim($p), "num", $conn);
                    if($pid == -1){ // not present in db
                        continue;
                    }

                    $query = "INSERT INTO course_to_course_relationship VALUES($pid, $id, 1)";
                    array_push($queries, $query);
                }
            }

            if(count($this->coreq) >= 1){
                foreach( $this->coreq as $c){
                    $cid = $this->getCourseIDFromDB(trim($c), "num", $conn);
                    if($cid == -1){
                        continue;
                    }

                    $query = "INSERT INTO course_to_course_relationship VALUES($id, $cid, 2)";
                    array_push($queries, $query);
                    $query = "INSERT INTO course_to_course_relationship VALUES($cid, $id, 2)";
                    array_push($queries, $query);
                }
            }

            $same_courses = $this->getSameCourses($conn);
            foreach( $same_courses as $c ){
                $query = "INSERT INTO course_to_course_relationship VALUES($id, $c, 3)";
                array_push($queries, $query);
            }

            $query = "INSERT INTO course_subject_area VALUES(".$id.",".$this->getSubjectArea($conn).")";
            array_push($queries, $query);

            debug_queries($queries, "ADD COURSE ".$this->num.": ".$this->title);

            $conn->beginTransaction();
            foreach($queries as $q){
                $conn->exec($q.';');
            }
            $conn->commit();

            foreach($this->sections as $s){
                $s->insert($conn);
            }
        }
        catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

}

class Section{

    public $semester;
    public $max_enroll;
    public $letter;
    public $building_id;
    public $professor_id;
    public $course_id;
    public $room_number;
    public $timeslot_ids = [];
    public $mod_type = null;

    function insert($conn)
    {
        $queries = [];

        $s = s($this->semester);
        $l = s($this->letter);

        $query = "INSERT INTO section VALUES($this->course_id, $l, $s, $this->max_enroll)";
        array_push($queries, $query);

        $r = s('_' . $this->room_number);
        $ts = $this->timeslot_ids[0];
        $query = "INSERT INTO meets_at VALUES($this->building_id, $l, $this->course_id, $s, $ts, $r)";
        array_push($queries, $query);

        $query = "INSERT INTO teaches VALUES($this->professor_id, $this->course_id, $l, $s)";
        array_push($queries, $query);

        if ($this->mod_type != null) {
            $query = "INSERT INTO section_mod VALUES($this->course_id, $l, $this->mod_type, $s)";
            array_push($queries, $query);
        }

        debug_queries($queries, "ADD SECTION ".$this->letter);

        try{
            $conn->beginTransaction();
            foreach ($queries as $q) {
                $conn->exec($q . ';');
            }
            $conn->commit();
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    function delete($conn){
        $queries = [];

        $s = s($this->semester);
        $l = s($this->letter);
        $r = s('_' . $this->room_number);

        $w = " WHERE course_id = $this->course_id AND section_letter = $l AND semester = $s";

        $tables = ["enrolls", "meets_at", "teaches", "section_mod"];

        foreach ( $tables as $t ){
            $query = "DELETE FROM ".$t.$w;
            array_push($queries, $query);
        }

        $query = "DELETE FROM section WHERE course_id = $this->course_id AND letter = $l AND semester = $s";
        array_push($queries, $query);

        debug_queries($queries, "DELETE SECTION ".$this->letter);

        try{
            $conn->beginTransaction();
            foreach ($queries as $q) {
                $conn->exec($q . ';');
            }
            $conn->commit();
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }

    function populate($conn, $course_id, $letter, $semester){
        try{
            $l = s($letter);
            $s = s($semester);
            $w = " WHERE course_id = $course_id AND letter = $l AND semester = $s";

            $query = "SELECT * FROM section".$w;
            $res = $conn->query($query);
            foreach ($res as $row){
                $this->max_enroll = $row['max_enroll'];
                $this->course_id = $row['course_id'];
                $this->letter = $row['letter'];
                $this->semester = $row['semester'];
            }

            $w = " WHERE course_id = $course_id AND section_letter = $l AND semester = $s";
            $query = "SELECT * FROM meets_at".$w;
            $res = $conn->query($query);
            foreach ($res as $row){
                $this->building_id = $row['building_id'];
                $this->room_number = trim($row['room_code'], '_');
                array_push($this->timeslot_ids, $row['time_slot_id']);
            }

            $query = "SELECT * FROM teaches".$w;
            $res = $conn->query($query);
            foreach ($res as $row){
                $this->professor_id = $row['professor_id'];
            }

            $query = "SELECT * FROM section_mod".$w;
            $res = $conn->query($query);
            foreach ($res as $row){
                if(isset($row['mod_type_id'])){
                    $this->mod_type = $row['mod_type_id'];
                }
            }
        } catch (PDOException $e){
            echo $e->getMessage();
            exit();
        }
    }
}