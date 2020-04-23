create Database Places;


create table places (
    tripit_id int(5) AUTO_INCREMENT PRIMARY KEY,
    place_id VARCHAR(30) NOT NULL,
    place_name VARCHAR (30) NOT NULL,
    place_type VARCHAR (10) NOT NULL,
    place_rating VARCHAR (10),
    place_lat VARCHAR (15) NOT NULL,
    place_lng VARCHAR (15) NOT NULL,
    place_icon VARCHAR (1000) NOT NULL,
    place_open VARCHAR (15),
    place_cover_iamge VARCHAR (1000),
    place_average_time (4) VARCHAR
)