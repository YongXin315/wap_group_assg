// MongoDB Schema for wap_system

// Create database
// use wap_system

// Create collections with validation schemas

// 1. Student Collection
db.createCollection("students", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["student_id", "student_email", "student_name", "password"],
      properties: {
        student_id: {
          bsonType: "string",
          description: "Student ID - required and must be unique"
        },
        student_email: {
          bsonType: "string",
          description: "Student email - required and must be unique"
        },
        student_name: {
          bsonType: "string",
          description: "Student name - required"
        },
        password: {
          bsonType: "string",
          description: "Hashed password - required"
        }
      }
    }
  }
});

// Create unique indexes
db.students.createIndex({ "student_id": 1 }, { unique: true });
db.students.createIndex({ "student_email": 1 }, { unique: true });

// 2. Admin Collection
db.createCollection("admins", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["admin_id", "admin_email", "password"],
      properties: {
        admin_id: {
          bsonType: "string",
          description: "Admin ID - required and must be unique"
        },
        admin_email: {
          bsonType: "string",
          description: "Admin email - required and must be unique"
        },
        password: {
          bsonType: "string",
          description: "Hashed password - required"
        }
      }
    }
  }
});

// Create unique indexes
db.admins.createIndex({ "admin_id": 1 }, { unique: true });
db.admins.createIndex({ "admin_email": 1 }, { unique: true });

// Insert admin data
db.admins.insertOne({
  admin_id: "ADM00001",
  admin_email: "taylorsadmin@taylors.edu.my",
  password: "$2y$10$ESXmQGWlVwtLwh5lijFM8uTOFzKMSpR5DwKb3VfGucsx2FbyU7iuG"
});

// 3. Room Collection
db.createCollection("rooms", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["room_id", "room_name", "block", "floor", "type", "min_occupancy", "max_occupancy", "amenities"],
      properties: {
        room_id: {
          bsonType: "string",
          description: "Room ID - required and must be unique"
        },
        room_name: {
          bsonType: "string",
          description: "Room name - required and must be unique"
        },
        block: {
          bsonType: "string",
          description: "Block - required"
        },
        floor: {
          bsonType: "string",
          description: "Floor - required"
        },
        type: {
          bsonType: "string",
          description: "Room type - required"
        },
        min_occupancy: {
          bsonType: "int",
          description: "Minimum occupancy - required"
        },
        max_occupancy: {
          bsonType: "int",
          description: "Maximum occupancy - required"
        },
        amenities: {
          bsonType: "string",
          description: "Amenities - required"
        },
        // Embedding class timetable directly in the room document
        class_timetable: {
          bsonType: "array",
          description: "Class timetable for this room",
          items: {
            bsonType: "object",
            required: ["day_of_week", "start_time", "end_time"],
            properties: {
              day_of_week: {
                enum: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                description: "Day of week - required"
              },
              start_time: {
                bsonType: "string",
                description: "Start time in HH:MM:SS format - required"
              },
              end_time: {
                bsonType: "string",
                description: "End time in HH:MM:SS format - required"
              }
            }
          }
        }
      }
    }
  }
});

// Create unique indexes
db.rooms.createIndex({ "room_id": 1 }, { unique: true });
db.rooms.createIndex({ "room_name": 1 }, { unique: true });

// Insert room data
db.rooms.insertMany([
  {
    room_id: "DR3.1",
    room_name: "Discussion Room 3.1",
    block: "C",
    floor: "3",
    type: "Discussion Room",
    min_occupancy: 4,
    max_occupancy: 8,
    amenities: "Whiteboard, TV with Wireless Display",
    class_timetable: [] // No classes scheduled for this room
  },
  {
    room_id: "C7.01",
    room_name: "Computer Lab C7.01",
    block: "C",
    floor: "7.01",
    type: "Computer Lab",
    min_occupancy: 1,
    max_occupancy: 30,
    amenities: "Whiteboard, TV with Wireless Display, 30 Computers",
    class_timetable: [] // No classes scheduled for this room
  },
  {
    room_id: "D8.01",
    room_name: "Classroom D8.01",
    block: "D",
    floor: "8.01",
    type: "Classroom",
    min_occupancy: 1,
    max_occupancy: 30,
    amenities: "Whiteboard, TV with Wireless Display",
    class_timetable: [] // No classes scheduled for this room
  },
  {
    room_id: "LT2",
    room_name: "Lecture Theatre 2",
    block: "B",
    floor: "1",
    type: "Lecture Theatre",
    min_occupancy: 1,
    max_occupancy: 200,
    amenities: "Whiteboard, TV with Wireless Display",
    class_timetable: [
      { day_of_week: "Monday", start_time: "09:00:00", end_time: "11:00:00" },
      { day_of_week: "Monday", start_time: "14:00:00", end_time: "16:00:00" },
      { day_of_week: "Tuesday", start_time: "10:00:00", end_time: "12:00:00" },
      { day_of_week: "Tuesday", start_time: "13:00:00", end_time: "14:00:00" },
      { day_of_week: "Wednesday", start_time: "09:00:00", end_time: "11:00:00" },
      { day_of_week: "Thursday", start_time: "12:00:00", end_time: "17:00:00" },
      { day_of_week: "Friday", start_time: "11:00:00", end_time: "13:00:00" }
    ]
  }
]);

// 4. Booking Collection
db.createCollection("bookings", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["student_id", "room_id", "room_name", "booking_date", "start_time", "end_time", "number_of_people", "created_at", "status"],
      properties: {
        booking_id: {
          bsonType: "objectId",
          description: "Booking ID - automatically generated"
        },
        student_id: {
          bsonType: "string",
          description: "Student ID - required"
        },
        room_id: {
          bsonType: "string",
          description: "Room ID - required"
        },
        room_name: {
          bsonType: "string",
          description: "Room name - required"
        },
        booking_date: {
          bsonType: "date",
          description: "Booking date - required"
        },
        start_time: {
          bsonType: "string",
          description: "Start time in HH:MM:SS format - required"
        },
        end_time: {
          bsonType: "string",
          description: "End time in HH:MM:SS format - required"
        },
        booking_purpose: {
          bsonType: "string",
          description: "Booking purpose"
        },
        number_of_people: {
          bsonType: "string",
          description: "Number of people - required"
        },
        created_at: {
          bsonType: "date",
          description: "Creation timestamp - required"
        },
        status: {
          enum: ["Pending Approval", "Approved", "Cancelled by Admin"],
          description: "Booking status - required"
        },
        approved_by_admin: {
          bsonType: "string",
          description: "Admin ID who approved the booking"
        }
      }
    }
  }
});

// Create indexes for faster queries
db.bookings.createIndex({ "student_id": 1 });
db.bookings.createIndex({ "room_id": 1 });
db.bookings.createIndex({ "booking_date": 1 });
db.bookings.createIndex({ "status": 1 });