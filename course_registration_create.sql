/* dropping tables */
  /*for relationships*/
DROP TABLE IF EXISTS course_to_course_relationship;
DROP TABLE IF EXISTS course_fee;
DROP TABLE IF EXISTS subject_area_division;
DROP TABLE IF EXISTS course_subject_area;
DROP TABLE IF EXISTS course_description;
DROP TABLE IF EXISTS belongs;
DROP TABLE IF EXISTS professor_office;
DROP TABLE IF EXISTS meets_at;
DROP TABLE IF EXISTS teaches;
DROP TABLE IF EXISTS enrolls;
DROP TABLE IF EXISTS section_mod;
DROP TABLE IF EXISTS program_requirement;
DROP TABLE IF EXISTS requirement_description;
 
 /*for entities*/
DROP TABLE IF EXISTS Section;
DROP TABLE IF EXISTS Course_Predicate;
DROP TABLE IF EXISTS Course;
DROP TABLE IF EXISTS Description;
DROP TABLE IF EXISTS Subject_Area;
DROP TABLE IF EXISTS Fee_Type;
DROP TABLE IF EXISTS Division;
DROP TABLE IF EXISTS Building;
DROP TABLE IF EXISTS Professor;
DROP TABLE IF EXISTS Time_Slot;
DROP TABLE IF EXISTS Student;

DROP TABLE IF EXISTS Requirement;
DROP TABLE IF EXISTS Program;
DROP TABLE IF EXISTS Program_Predicates;
DROP TABLE IF EXISTS Mod_Type;
DROP TABLE IF EXISTS Users;






/* CREATING TABLES */
  /*For Entities:*/
CREATE TABLE Course_Predicate (
  id INT PRIMARY KEY,
  meaning CHAR(15) NOT NULL UNIQUE
);

CREATE TABLE Course (
  id INT PRIMARY KEY,
  num CHAR(15) NOT NULL UNIQUE,
  title VARCHAR(200) NOT NULL,
  credits INT NOT NULL,
  level INT NOT NULL
);


CREATE TABLE Description (
  id INT PRIMARY KEY,
  descript TEXT NOT NULL
);



CREATE TABLE Subject_Area (
  id INT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Fee_Type (
  type CHAR(6) PRIMARY KEY,
  amount INT NOT NULL UNIQUE
);

CREATE TABLE Division (
  id INT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Building (
  id INT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Professor (
  id INT PRIMARY KEY,
  first_name VARCHAR(20) NOT NULL,
  last_name VARCHAR(20) NOT NULL,
  email VARCHAR(50),
  phone CHAR(12)
);

CREATE TABLE Time_Slot (
  id INT PRIMARY KEY,
  time_start TIME NOT NULL,
  time_end TIME NOT NULL,
  day CHAR(1) NOT NULL
);

CREATE TABLE Student (
  id INT PRIMARY KEY,
  first_name VARCHAR(20) NOT NULL,
  last_name VARCHAR(20) NOT NULL,
  email VARCHAR(50)
);


CREATE TABLE Section (
  course_id INT,
  letter CHAR(1),
  semester CHAR(10),
  max_enroll INT,
  PRIMARY KEY(course_id, letter, semester),
  FOREIGN KEY(course_id) REFERENCES Course(id)
);

CREATE TABLE Requirement (
  id INT PRIMARY KEY,
  meaning VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE Program (
  id INT PRIMARY KEY,
  title VARCHAR(50) NOT NULL UNIQUE,
  purpose CHAR(13) NOT NULL
);

CREATE TABLE Program_Predicates (
  id INT PRIMARY KEY,
  meaning VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE Mod_Type (
  id INT PRIMARY KEY,
  start_date DATE,
  end_date DATE,
  name CHAR(5) NOT NULL
);

CREATE TABLE Users(
  username VARCHAR(100),
  password TEXT
);


  /*For Relationships:*/
CREATE TABLE course_to_course_relationship (
  course1_id INT,
  course2_id INT,
  course_predicate_id INT,
  PRIMARY KEY (course1_id,course2_id,course_predicate_id),
  FOREIGN KEY(course1_id) REFERENCES Course(id),
  FOREIGN KEY(course2_id) REFERENCES Course(id)
);

CREATE TABLE course_fee (
  course_id INT,
  Fee_Type CHAR(6),
  PRIMARY KEY(course_id,Fee_Type),
  FOREIGN KEY(course_id) REFERENCES Course(id),
  FOREIGN KEY(Fee_Type) REFERENCES Fee_Type(type)
);

CREATE TABLE subject_area_division (
  subject_area_id INT,
  division_id INT,
  PRIMARY KEY(subject_area_id, division_id),
  FOREIGN KEY(subject_area_id) REFERENCES Subject_Area(id),
  FOREIGN KEY(division_id) REFERENCES Division(id)
);

CREATE TABLE course_subject_area (
  course_id INT,
  subject_area_id INT,
  PRIMARY KEY(course_id, subject_area_id),
  FOREIGN KEY(course_id) REFERENCES Course(id),
  FOREIGN KEY(subject_area_id) REFERENCES Subject_Area(id)
);


CREATE TABLE course_description(
  course_id INT,
  description_id INT,
  PRIMARY KEY(course_id, description_id),
  FOREIGN KEY(course_id) REFERENCES Course(id),
  FOREIGN KEY(description_id) REFERENCES Description(id)
);

CREATE TABLE belongs(
  division_id INT,
  professor_id INT,
  PRIMARY KEY(division_id, professor_id),
  FOREIGN KEY(division_id) REFERENCES Division(id),
  FOREIGN KEY(professor_id) REFERENCES Professor(id)
);

CREATE TABLE professor_office(
  professor_id INT,
  building_id INT,
  room_code CHAR(7),
  PRIMARY KEY(building_id, professor_id),
  FOREIGN KEY(building_id) REFERENCES Building(id),
  FOREIGN KEY(professor_id) REFERENCES Professor(id)
);

CREATE TABLE meets_at(
  building_id INT,
  section_letter CHAR(1),
  course_id INT,
  semester CHAR(10),
  time_slot_id INT,
  room_code CHAR(7) NOT NULL,
  PRIMARY KEY(building_id, section_letter, course_id, time_slot_id, semester),
  FOREIGN KEY(building_id) REFERENCES Building(id),
  FOREIGN KEY(course_id, section_letter, semester) REFERENCES Section(course_id, letter, semester),
  FOREIGN KEY(time_slot_id) REFERENCES Time_Slot(id)
);

CREATE TABLE teaches(
  professor_id INT,
  course_id INT,
  section_letter CHAR(1),
  semester CHAR(10), 
  PRIMARY KEY(professor_id, course_id, section_letter,semester),
  FOREIGN KEY(professor_id) REFERENCES Professor(id),
  FOREIGN KEY(course_id, section_letter,semester) REFERENCES Section(course_id, letter,semester)
);

CREATE TABLE enrolls(
  student_id INT,
  course_id INT,
  section_letter CHAR(1),
  semester CHAR(10), 
  grade CHAR(2),
  state VARCHAR(20) NOT NULL,
  PRIMARY KEY(course_id, section_letter, student_id,semester),
  FOREIGN KEY(course_id, section_letter,semester) REFERENCES Section(course_id, letter,semester),
  FOREIGN KEY(student_id) REFERENCES Student(id)
);

CREATE TABLE section_mod(
  course_id INT,
  section_letter CHAR(1),
  mod_type_id INT,
  semester CHAR(10), 
  PRIMARY KEY(course_id, section_letter, mod_type_id,semester),
  FOREIGN KEY(course_id, section_letter,semester) REFERENCES Section(course_id, letter,semester),
  FOREIGN KEY(mod_type_id) REFERENCES Mod_Type(id)
);

CREATE TABLE program_requirement(
  program_id INT,
  requirement_id INT,
  PRIMARY KEY(program_id, requirement_id),
  FOREIGN KEY(program_id) REFERENCES Program(id),
  FOREIGN KEY(requirement_id) REFERENCES Requirement(id)
);

CREATE TABLE requirement_description(
  requirement_id INT,
  program_predicates_id INT,
  value INT NOT NULL,
  PRIMARY KEY(requirement_id, program_predicates_id,value),
  FOREIGN KEY(requirement_id) REFERENCES Requirement(id),
  FOREIGN KEY(program_predicates_id) REFERENCES Program_Predicates(id)
);




/* dropping views */
DROP VIEW IF EXISTS current_enrollment_for_sections;
DROP VIEW IF EXISTS section_time_location;
DROP VIEW IF EXISTS program_requirements;


/* creating views */

CREATE VIEW current_enrollment_for_sections AS
  SELECT e.course_id, e.section_letter, count(e.student_id) as no_of_students_enrolled
    FROM Section s, enrolls e 
    WHERE e.state = "enrolled" 
    AND s.course_id = e.course_id 
    AND s.letter = e.section_letter
    GROUP BY e.course_id, e.section_letter;


CREATE VIEW section_time_location AS 
  SELECT s.course_id, s.letter, t.time_start, t.time_end, t.day, b.name, m.room_code
    FROM Section s, meets_at m, Time_Slot t, Building b 
    WHERE s.course_id = m.course_id 
    AND s.letter = m.section_letter 
    AND m.time_slot_id = t.id
    AND m.building_id = b.id;


CREATE VIEW program_requirements AS
  SELECT p.title, r.meaning 
  FROM Program p, Requirement r, program_requirement pr
  WHERE pr.program_id = p.id
  AND pr.requirement_id = r.id
  GROUP BY p.title, r.meaning;

