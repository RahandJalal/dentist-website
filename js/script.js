
// ==========================================
// 1. CONFIGURATION
// ==========================================
const CHATBOT_API_KEY = 'AIzaSyAKxkMFjo9xjg4eOZivQ1RM4tNeRU4J-aI';
const API_URL = 'api.php';

// ==========================================
// 2. APPOINTMENT MODAL POPUP LOGIC
// ==========================================
function openAppointmentModal() {
   const modal = document.getElementById('appointmentModal');
   if (modal) {
      modal.classList.add('active');
   }
}

function closeAppointmentModal() {
   const modal = document.getElementById('appointmentModal');
   if (modal) {
      modal.classList.remove('active');
   }
}

window.addEventListener('click', function(e) {
   const modal = document.getElementById('appointmentModal');
   if (e.target === modal) {
      closeAppointmentModal();
   }
});

// ==========================================
// 3. GREYING OUT BOOKED TIME SLOTS
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
   const doctorSelect = document.getElementById('doctor-select');
   const dateInput = document.querySelector('input[name="date"]');
   const timeSelect = document.getElementById('appointment-time');

   if (doctorSelect && dateInput && timeSelect) {
      function checkBookedSlots() {
         const doctor = doctorSelect.value;
         const date = dateInput.value;

         if (!doctor || !date) return;

         fetch(`${API_URL}?action=check_slots&doctor=${encodeURIComponent(doctor)}&date=${encodeURIComponent(date)}`, {
            method: 'GET',
            headers: {
               'X-API-KEY': CHATBOT_API_KEY
            }
         })
         .then(res => res.json())
         .then(data => {
            const bookedTimes = data.booked_slots || [];
            timeSelect.value = '';

            Array.from(timeSelect.options).forEach(option => {
               if (!option.value) return;

               if (bookedTimes.includes(option.value)) {
                  option.disabled = true;
                  option.textContent = option.value + " (Booked)";
                  option.style.backgroundColor = "#e2e8f0";
                  option.style.color = "#a0aec0";
               } else {
                  option.disabled = false;
                  option.textContent = option.value;
                  option.style.backgroundColor = "";
                  option.style.color = "";
               }
            });
         })
         .catch(err => console.error("Error fetching slots:", err));
      }

      doctorSelect.addEventListener('change', checkBookedSlots);
      dateInput.addEventListener('change', checkBookedSlots);
   }
});

// ==========================================
// 4. NAVIGATION BAR & HEADER SCROLL
// ==========================================
let menu = document.querySelector('#menu-btn');
let navbar = document.querySelector('.header .nav');
let header = document.querySelector('.header');

if (menu && navbar) {
   menu.onclick = () => {
      menu.classList.toggle('fa-times');
      navbar.classList.toggle('active');
   };
}

window.onscroll = () => {
   if (menu) menu.classList.remove('fa-times');
   if (navbar) navbar.classList.remove('active');

   if (header) {
      if (window.scrollY > 0) {
         header.classList.add('active');
      } else {
         header.classList.remove('active');
      }
   }
};

// ==========================================
// 5. CHATBOT INTERFACE & API CONNECTION
// ==========================================
const chatToggle = document.getElementById("chat-toggle");
const chatWidget = document.getElementById("chat-widget");
const chatClose = document.getElementById("chat-close");
const chatBody = document.getElementById("chat-body");
const chatInput = document.getElementById("chat-input");
const chatSend = document.getElementById("chat-send");

if (chatToggle && chatWidget) {
   chatToggle.onclick = () => {
      chatWidget.classList.toggle("active");
   };
}

if (chatClose && chatWidget) {
   chatClose.onclick = () => {
      chatWidget.classList.remove("active");
   };
}

function addBotMessage(text) {
   if (!chatBody) return;
   const msg = document.createElement("div");
   msg.className = "bot-message";
   msg.style.whiteSpace = "pre-line";
   msg.innerText = text;
   chatBody.appendChild(msg);
   chatBody.scrollTop = chatBody.scrollHeight;
}

function addUserMessage(text) {
   if (!chatBody) return;
   const msg = document.createElement("div");
   msg.className = "user-message";
   msg.style.whiteSpace = "pre-line";
   msg.innerText = text;
   chatBody.appendChild(msg);
   chatBody.scrollTop = chatBody.scrollHeight;
}

function sendChatMessage() {
   if (!chatInput) return;
   const message = chatInput.value.trim();
   if (!message) return;

   // 1. Show user message
   addUserMessage(message);
   chatInput.value = '';

   // 2. Query api.php
   fetch(API_URL, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'X-API-KEY': CHATBOT_API_KEY
      },
      body: JSON.stringify({
         action: 'chat',
         message: message
      })
   })
   .then(res => res.json())
   .then(data => {
      if (data.status === 'success') {
         addBotMessage(data.reply);
      } else {
         addBotMessage("Sorry, I ran into an error. Please check your API key.");
      }
   })
   .catch(err => {
      console.error(err);
      addBotMessage("Unable to reach the server.");
   });
}

if (chatSend) {
   chatSend.onclick = sendChatMessage;
}

if (chatInput) {
   chatInput.addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
         e.preventDefault();
         sendChatMessage();
      }
   });
}

// ==========================================
// 6. QUICK-BUTTON ACTIONS IN CHAT
// ==========================================
function startBooking() {
   addUserMessage("I want to book an appointment");
   addBotMessage("Opening the appointment form for you...");
   openAppointmentModal();
}

function showServices() {
   addUserMessage("Our services");
   addBotMessage("We provide:\n• Alignment & Braces\n• Cosmetic Dentistry\n• Oral Hygiene\n• Root Canal\n• Dental Implants");
}

function showContact() {
   addUserMessage("Contact information");
   addBotMessage("📍 Location: Erbil, Kurdistan\n📞 Phone: +123-456-7890\n🕒 Hours: 9:00 AM – 11:00 PM");
}

// Expose functions globally for inline HTML onclick attributes
window.openAppointmentModal = openAppointmentModal;
window.closeAppointmentModal = closeAppointmentModal;
window.startBooking = startBooking;
window.showServices = showServices;
window.showContact = showContact;