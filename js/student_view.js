var added_course_arr = []; //eg. CMPT 100A
var added_course_arr_of_obj = [];


function get_all_current_courses () {
    load_data("get_all_current_courses","data");
}





function add_to_schedule(num,letter){
    var course_num = num;
    var section_letter = letter;

    /* var new_course_obj;
    if (added_course_arr_of_obj.length > 0) {
        new_course_obj = {};
    }
    */
    var new_course_obj = {};
    
    var course_obj = {"new_course": {"course_num": course_num, "section_letter": section_letter}, "previous_added_courses": added_course_arr_of_obj};

    var course_num_letter = course_num + section_letter;
    
    if (!added_course_arr.includes(course_num_letter)) {
        load_data("check_overlap",course_obj);
        
        console.log(course_obj)
        // adding to added_course_arr
        added_course_arr.push(course_num_letter);

        // adding to added course
        if (added_course_arr_of_obj.length > 0) {
            added_course_arr_of_obj.push(new_course_obj); 
        }
        else {
            //new_course_obj['new']
        }   
        added_course_arr_of_obj.push(new_course_obj); 
    }
    else {
        alert("Already added this course");
    }
}

// get data from server
function load_data(request_status,data) {
    var xmlhttp;
	if (window.XMLHttpRequest){
		//  IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else{
		// IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    var file_name = "";
    var params = "";
    if (request_status === "get_all_current_courses") {
        file_name = "student_courses.php";
        params = request_status + "=" + data;

    }
    

	xmlhttp.open("POST", file_name, true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

   

	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
            //var JSON_data = JSON.parse(xmlhttp.responseText);
            //console.log(JSON_data);
            
            //var text = xmlhttp.responseText;

            //add_course_to_schedule(text);
            
		}
	};	
	xmlhttp.send(params);
}


function add_course_to_schedule(text) {
    //for testing
    length_of_input_arr = 2

    var table_element = document.getElementById("addToScheduleTable");
    var tr_element = document.createElement('tr');

    for (var i=0; i < length_of_input_arr; i++) {
        if (i == length_of_input_arr-1) {
            var remove_button_element = document.createElement('button');
            remove_button_element.innerHTML = "Remove";

            remove_text = "delete_course('" + text + "')";
            remove_button_element.setAttribute("onclick",remove_text);
            remove_button_element

            tr_element.appendChild(remove_button_element);
        }
        else {
            var td_element = document.createElement('td');
            td_element.innerHTML = text;
    
            tr_element.appendChild(td_element);
        }
    }
    table_element.appendChild(tr_element);
}



function delete_course(c_number_section) {
    var index = added_course_arr.indexOf(c_number_section) + 1;
    
    var table_element = document.getElementById("addToScheduleTable");
    table_element.deleteRow(index);


    index = added_course_arr.indexOf(c_number_section);
    if (index > -1) {
        added_course_arr.splice(index, 1);
    }
}

