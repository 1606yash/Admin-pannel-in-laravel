function initializeCalendar(response, monthId, yearId) {
    var calendarEl = document.getElementById('calendar');
    const endDate = new Date(yearId, monthId, 0);
    endDate.setDate(endDate.getDate() + 1);
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: '',
        center: 'title',
        right: "",
        }, 
        validRange: { 
            start: new Date(yearId, monthId - 1, 1), 
            end: endDate,
        },
        dayCellContent: function (arg) {
            var dayNumber = arg.dayNumberText;
            var isoDate = new Date(Date.UTC(arg.date.getFullYear(), arg.date.getMonth(), arg.date.getDate())).toISOString().split('T')[0];

            // Create two circles for "M"
            var circlesM = document.createElement('div');
            circlesM.classList.add('circle-container');
            for (var i = 0; i < 2; i++) {
                var circle = document.createElement('div');
                circle.classList.add('circle');
                circle.style.color = 'white'; // Set text color to white
                circle.innerText = 'M';
                circlesM.appendChild(circle);
        
                // Find staff for "M" circles based on conditions
                var staff = response.staffList.find(staff => {
                    const dateMatches = staff.date === isoDate;
                    return dateMatches && staff.shift_type_id === 1 && staff.user_type === (i === 0 ? 'Driver' : 'Attendant');
                });
        
                // Set color for "M" circles
                circle.style.backgroundColor = staff ? 'green' : 'red';
            }
        
            // Create two circles for "E"
            var circlesE = document.createElement('div');
            circlesE.classList.add('circle-container');
            for (var i = 0; i < 2; i++) {
                var circle = document.createElement('div');
                circle.classList.add('circle');
                circle.style.color = 'white'; // Set text color to white
                circle.innerText = 'E';
                circlesE.appendChild(circle);
        
                // Find staff for "E" circles based on conditions
                var staff = response.staffList.find(staff => {
                    const dateMatches = staff.date === isoDate;
                    return dateMatches && staff.shift_type_id === 2 && staff.user_type === (i === 0 ? 'Driver' : 'Attendant');
                });
        
                // Set color for "E" circles
                circle.style.backgroundColor = staff ? 'green' : 'red';
            }
        
            // Create a container div for the day content
            var content = document.createElement('div');
            content.appendChild(document.createTextNode(dayNumber));
            content.appendChild(circlesM);
            content.appendChild(circlesE);
        
            return { domNodes: [content] };
        }  
    });
    calendar.render();
}
function initializeCalendarInModal(response, monthId, yearId) {
    var calendarEl = document.getElementById('calendar-single');
    const endDate = new Date(yearId, monthId, 0);
    endDate.setDate(endDate.getDate() + 1);

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: '',
            center: '',
            right: "",
        }, 
        validRange: { 
            start: new Date(yearId, monthId - 1, 1), 
            end: endDate,
        },
        dayCellContent: function (arg) {
            var dayNumber = arg.date.getDate();
            var isoDate = new Date(Date.UTC(arg.date.getFullYear(), arg.date.getMonth(), arg.date.getDate())).toISOString().split('T')[0];

            var circles = document.createElement('div');
            circles.classList.add('circle-container');

            // Create the first circle with white text and colored background
            var circle = document.createElement('div');
            circle.classList.add('circle');
            circle.style.color = 'white'; // Set text color to white
            circle.style.backgroundColor = 'red'; // Default background color
            if(response.shift_type_id == 1) {
                circle.innerText = 'M';
            } else if(response.shift_type_id == 2) {
                circle.innerText = 'E';
            }
            // Check for matching staff assignments and update background color
            var staff = response.staffList.find(staff => staff.date === isoDate);
            if (staff) {
                circle.style.backgroundColor = 'green';
            }

            circles.appendChild(circle);

            // Create the container for day content
            var content = document.createElement('div');
            content.appendChild(document.createTextNode(dayNumber));
            content.appendChild(circles);

            return { domNodes: [content] };
        }    
    });
    calendar.render();
}