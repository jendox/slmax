DROP TABLE IF EXISTS person;
CREATE TABLE person (
    id INT NOT NULL,
    firstname VARCHAR(32),
    lastname VARCHAR(32),
    birthdate DATE,
    gender BIT,
    birthplace VARCHAR(32),
    PRIMARY KEY (id)
);