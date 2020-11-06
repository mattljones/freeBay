/* 
*** freeBay DB generation script incl. sample data ***
*/


/*
*** BASIC DB CREATION ***
*/

-- Drop previous copies of DB if exist
DROP DATABASE IF EXISTS Freebay;

-- New DB and 'admin' user creation (permissions granted one table at a time after table creation to avoid granting privileges on 'Defaults' table (full discussion in report))
CREATE DATABASE Freebay
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'adminpassword';

-- Select correct DB for use 
USE Freebay;

-- Table creation (x8, excl. FK constraints)
CREATE TABLE Buyers (
    buyerID INT UNSIGNED AUTO_INCREMENT,  
    username VARCHAR(20) NOT NULL,
    email VARCHAR(254) NOT NULL,
    pass CHAR(60) NOT NULL,
    firstName VARCHAR(35) NOT NULL,
    familyName VARCHAR(35) NOT NULL,
    telNo BIGINT UNSIGNED,   -- Null allowed (optional user input)
    addressID INT UNSIGNED,  -- Null allowed (optional user input)
    CONSTRAINT Buyers_pk PRIMARY KEY (buyerID),
    CONSTRAINT Buyers_ck UNIQUE (username)
    )
    ENGINE = InnoDB;

CREATE TABLE Sellers (
    sellerID INT UNSIGNED AUTO_INCREMENT  ,
    username VARCHAR(20) NOT NULL,
    email VARCHAR(254) NOT NULL,
    pass CHAR(60) NOT NULL,
    firstName VARCHAR(35) NOT NULL,
    familyName VARCHAR(35) NOT NULL,
    telNo BIGINT UNSIGNED,   -- Null allowed (optional user input)
    addressID INT UNSIGNED,  -- Null allowed (optional user input)
    CONSTRAINT Sellers_pk PRIMARY KEY (sellerID),
    CONSTRAINT Sellers_ck UNIQUE (username)
    )
    ENGINE = InnoDB;

CREATE TABLE Addresses (
    addressID INT UNSIGNED AUTO_INCREMENT,
    line1 VARCHAR(35) NOT NULL,
    city VARCHAR(35) NOT NULL,
    postcode VARCHAR(35) NOT NULL,
    countryID TINYINT UNSIGNED NOT NULL,
    CONSTRAINT Addresses_pk PRIMARY KEY (addressID)
    )
    ENGINE = InnoDB;

CREATE TABLE Countries (
    countryID TINYINT UNSIGNED AUTO_INCREMENT,
    countryName VARCHAR(35) NOT NULL,
    CONSTRAINT Countries_pk PRIMARY KEY (countryID),
    CONSTRAINT Countries_ck UNIQUE (countryName)
    )
    ENGINE = InnoDB;

CREATE TABLE Bids (
    bidID BIGINT UNSIGNED AUTO_INCREMENT,
    bidDate DATETIME NOT NULL,
    bidAmount DECIMAL(9,2) UNSIGNED NOT NULL,
    buyerID INT UNSIGNED NOT NULL DEFAULT 1,
    auctionID BIGINT UNSIGNED NOT NULL,
    CONSTRAINT Bids_pk PRIMARY KEY (bidID)
    )
    ENGINE = InnoDB;

CREATE TABLE Watching (
    buyerID INT UNSIGNED,
    auctionID BIGINT UNSIGNED,
    CONSTRAINT Watching_pk PRIMARY KEY (buyerID, auctionID)
    )
    ENGINE = InnoDB;

CREATE TABLE Auctions (
    auctionID BIGINT UNSIGNED AUTO_INCREMENT,
    title VARCHAR(80) NOT NULL,
    descript VARCHAR(4000) NOT NULL,
    createDate DATETIME NOT NULL,
    startDate DATETIME NOT NULL,
    endDate DATETIME NOT NULL,
    startPrice DECIMAL(9,2) UNSIGNED NOT NULL,
    reservePrice DECIMAL(9,2) UNSIGNED NOT NULL,
    minIncrement DECIMAL(8,2) UNSIGNED NOT NULL,
    sellerID INT UNSIGNED NOT NULL DEFAULT 1,               
    categoryID TINYINT UNSIGNED NOT NULL DEFAULT 1,     
    CONSTRAINT Auctions_pk PRIMARY KEY (auctionID),
    CONSTRAINT CHK_startDate CHECK (startDate >= createDate),         -- Simple inter-value validity check
    CONSTRAINT CHK_endDate CHECK (endDate > startDate),               -- Simple inter-value validity check
    CONSTRAINT CHK_reservePrice CHECK (reservePrice >= startPrice)    -- Simple inter-value validity check
    )
    ENGINE = InnoDB;

CREATE TABLE Categories (
    categoryID TINYINT UNSIGNED AUTO_INCREMENT,
    categoryName VARCHAR(35) NOT NULL,
    CONSTRAINT Categories_pk PRIMARY KEY (categoryID),
    CONSTRAINT Categories_ck UNIQUE (categoryName)
    )
    ENGINE = InnoDB;

-- Granting permissions to 'admin' one-by-one (full discussion in report)
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Buyers TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Sellers TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Addresses TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Countries TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Bids TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Watching TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Auctions TO 'admin'@'localhost';
GRANT SELECT, UPDATE, INSERT, DELETE ON Freebay.Categories TO 'admin'@'localhost';


/*
*** ADDING FKs (x9) AND REFERENTIAL INTEGRITY CONSTRAINTS (ON DELETE, ON UPDATE) IN 4 GROUPS ***
*/

-- Group 1: ON DELETE, NO ACTION (deletion is most likely a mistake; full discussion in report)
ALTER TABLE Addresses 
    ADD CONSTRAINT Addresses_fk FOREIGN KEY (countryID) 
    REFERENCES Countries(countryID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION;

-- Group 2: ON DELETE, SET NULL (data the FK is referencing is optional for users on sign-up; full discussion in report)
ALTER TABLE Buyers 
    ADD CONSTRAINT Buyers_fk FOREIGN KEY (addressID) 
    REFERENCES Addresses(addressID)
    ON UPDATE CASCADE
    ON DELETE SET NULL;

ALTER TABLE Sellers 
    ADD CONSTRAINT Sellers_fk FOREIGN KEY (addressID) 
    REFERENCES Addresses(addressID)
    ON UPDATE CASCADE
    ON DELETE SET NULL;

-- Group 3: ON DELETE, SET DEFAULT (allows column to maintain no nulls; full discussion in report)
ALTER TABLE Bids 
    ADD CONSTRAINT Bids_fk1 FOREIGN KEY (buyerID) 
    REFERENCES Buyers(buyerID)
    ON UPDATE CASCADE
    ON DELETE SET DEFAULT;

ALTER TABLE Auctions 
    ADD CONSTRAINT Auctions_fk1 FOREIGN KEY (sellerID) 
    REFERENCES Sellers(sellerID)
    ON UPDATE CASCADE
    ON DELETE SET DEFAULT;

ALTER TABLE Auctions 
    ADD CONSTRAINT Auctions_fk2 FOREIGN KEY (categoryID) 
    REFERENCES Categories(categoryID)
    ON UPDATE CASCADE
    ON DELETE SET DEFAULT;

-- Group 4: ON DELETE, CASCADE (information is no longer relevant without parent entity; full discussion in report)
ALTER TABLE Bids 
    ADD CONSTRAINT Bids_fk2 FOREIGN KEY (auctionID) 
    REFERENCES Auctions(auctionID)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE Watching 
    ADD CONSTRAINT Watching_fk1 FOREIGN KEY (buyerID) 
    REFERENCES Buyers(buyerID)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE Watching 
    ADD CONSTRAINT Watching_fk2 FOREIGN KEY (auctionID) 
    REFERENCES Auctions(auctionID)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

-- Create a 'Defaults' table referencing Group 3 (above) parent tables' PKs with a FK constraint to prevent deletion of default values (full discussion in report)
CREATE TABLE Defaults (
    buyerID INT UNSIGNED NOT NULL,
    sellerID INT UNSIGNED NOT NULL,
    categoryID TINYINT UNSIGNED NOT NULL
    )
    ENGINE = InnoDB;

ALTER TABLE Defaults 
    ADD CONSTRAINT Defaults_fk1 FOREIGN KEY (buyerID) 
    REFERENCES Buyers(buyerID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION;

ALTER TABLE Defaults 
    ADD CONSTRAINT Defaults_fk2 FOREIGN KEY (sellerID) 
    REFERENCES Sellers(sellerID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION;

ALTER TABLE Defaults 
    ADD CONSTRAINT Defaults_fk3 FOREIGN KEY (categoryID) 
    REFERENCES Categories(categoryID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION;

INSERT INTO Buyers (username, email, pass, firstName, familyName)                                       -- Inserted at position '1' in Buyers table
  VALUES ('N/A', 'N/A', '000000000000000000000000000000000000000000000000000000000000', 'N/A', 'N/A');  
INSERT INTO Sellers (username, email, pass, firstName, familyName)                                      -- Inserted at position '1' in Sellers table
  VALUES ('N/A', 'N/A', '000000000000000000000000000000000000000000000000000000000000', 'N/A', 'N/A');
INSERT INTO Categories (categoryName)                                                                   -- Inserted at position '1' in Categories table 
  VALUES ('Other');                                                 
INSERT INTO Defaults (buyerID, sellerID, categoryID) 
  VALUES (1, 1, 1);                                  


/*
*** SAMPLE DATA ***
*/

-- Disable FK checks when importing
SET FOREIGN_KEY_CHECKS = 0;

-- Buyers x10 (in addition to the default buyer created for the Defaults table)
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('requirehaggard', 'pajas@optonline.net', '335e931861dbbb8bf426bff539a6bd0e6513bf5ebff539a6bd0e6513bf5e', 'Callie', 'Christie', 12025550169, 1);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('suitgrin', 'skajan@icloud.com', '9d449192f036bb3d4e474303d67b8c9c3642d0c24303d67b8c9c3642d0c2', 'Nela', 'Beil', 12025550119, 2);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('educatorlyrical', 'msloan@msn.com', 'd6c9ece0eb4162bff9521ce5e35cc7f065022ea91ce5e35cc7f065022ea9', 'Alys', 'Hughes', 12025550106, 3);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('hardballtalented', 'quinn@hotmail.com', 'def3861b9d65fd41d0b0a5c1d19affdfe6f495eca5c1d19affdfe6f495ec', 'Abbas', 'Mcdermott', 12025550141, 4);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('unluckyresource', 'tarreau@live.com', '90d2a3ae2546f6eda26c042a244e14bec3a106ea042a244e14bec3a106ea', 'Kayla', 'Hurley', 12025550113, 5);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('bowspritcalling', 'tarreau@live.com', 'cb95f5661150e083e14658e0c62463854a99b6a758e0c62463854a99b6a7', 'Meadow', 'Suarez', 12025550172, 6);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('drabitbedight', 'qmacro@optonline.net', 'f6e523897e5920e01c862ee99d47ed73f2baec322ee99d47ed73f2baec32', 'Rebecca', 'Quintero', 441632960259, 7);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('cheelpervous', 'wiseb@hotmail.com', '55a69fa7bb1244a0b7fa3f8c55863096157d0ae93f8c55863096157d0ae9', 'Jayden', 'Emerson', 441632960095, 7);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo)
  VALUES ('utterwhaffle', 'greear@att.net', 'cf05431582772c8b46e25e9297ffe361d61d1d6e5e9297ffe361d61d1d6e', 'Roxy', 'Garrett', 441632960947);
INSERT INTO Buyers (username, email, pass, firstName, familyName, addressID)
  VALUES ('exultantwithers', 'twoflower@comcast.net', '607424177515eb2e1688a17b339a0ecf8665769ea17b339a0ecf8665769e', 'Katelyn', 'Gordon', 8);

-- Sellers x10 (in addition to the default seller created for the Defaults table)
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('rulesuper', 'carcus@optonline.net', '15feef93cbcf13e03ae482b0a1c99ac209c61fef82b0a1c99ac209c61fef', 'Jaxon', 'Daniels', 441632960541, 9);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('coastfoking', 'mdielmann@optonline.net', 'c179147c8de2421e12d07bbe84d84883760568bf7bbe84d84883760568bf', 'Nojus', 'Grainger', 441632960324, 10);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('scourgetrotwood', 'mallanmba@optonline.net', 'beaf00795f5bb717e2d3168cf8e47db13b72fc99168cf8e47db13b72fc99', 'Shantelle', 'Stevenson', 441632960259, 11);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('countmatter', 'jcholewa@verizon.net', '59cde38a289b79ff64fda59ff740b43b1ccbc5f1a59ff740b43b1ccbc5f1', 'Beatrice', 'Mcmanus', 81759267428, 12);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('politicsduro', 'bester@gmail.com', '8033814191d11483fb9ba2eb314fac5bdcf1ef98a2eb314fac5bdcf1ef98', 'Herman', 'Gunn', 81753771329, 13);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('sensualbeginner', 'msusa@gmail.com', 'f0df0f92ff7b707a78c75c8d3c15ff74ac1785105c8d3c15ff74ac178510', 'Olivia-Rose', 'Handley', 81752700372, 14);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('vardknobby', 'neuffer@me.com', 'e55a513215f5e8a4a5409beaac6ff5b01b22c0319beaac6ff5b01b22c031', 'Rachel', 'Rangel', 81755858066, 15);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('quoteelectronic', 'houle@sbcglobal.net', 'cd1cfc4d821a182c0793e50db2cd015deb922063e50db2cd015deb922063', 'Carwyn', 'Mendez', 81755473558, 15);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo)
  VALUES ('gashfordmocolate', 'sonnen@me.com', '8429c3436bcc238b8d9720daeb0974eba7cab02720daeb0974eba7cab027', 'Regina', 'Mccabe', 81755473558);
INSERT INTO Sellers (username, email, pass, firstName, familyName, addressID)
  VALUES ('rovelyeregion', 'mglee@yahoo.ca', 'fa8de636191d8b60d9fa0e62b7825f238db3f0040e62b7825f238db3f004', 'Moesha', 'Gallagher', 16);

-- Addresses x16
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('216 North High Point Street', 'Beverly', '01915', 187);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('19 Central Street', 'Key West', '33040', 187);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('38 Pilgrim Court', 'Ogden', '84404', 187);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('29 San Juan Drive', 'Augusta', '30906', 187);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('8540 S. Glenholme Drive', 'La Vergne', '37086', 187);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('534 Valley Street', 'Oviedo', '32765', 187);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('65 Boroughbridge Road', 'Birmingham', 'B5 3DL', 186); 
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('12 Cloch Rd', 'St Michaels', 'WR15 8XE', 186);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('22 Bridge Street', 'Goginan', 'SY23 2JU', 186);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('59 Ilchester Road', 'Murton', 'TD15 2PS', 186);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('92 Felix Lane', 'Shrawardine', 'SY4 2LN', 186);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('4454 Hastings Street', 'Vancouver', 'V6C 1B4', 33);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('2854 Rayborn Crescent', 'St Albert', 'T8N 1C7', 33);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('1344 St. John Street', 'Big River', 'S4P 3Y2', 33);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('51 Burnt Island Road', 'Port Sydney', 'P0B 1L0', 33);
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('384 Thurston Dr', 'Ottowa', 'K1P 5G8', 33);

-- Auctions x15
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('1900s postcard from St Mary''s Church in Warwick', 
          'Vintage postcard from the early 1900s. St Mary''s Church in Warwick. For Christmas Day but not posted, in excellent condition', 
          '2020-11-05T12:34:23', '2020-11-05T12:34:23', '2020-11-06T12:34:23', 3.00, 5.00, 0.01, 1, 2);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('1080p 42 inch Samsung flat screen TV. Top of the range and brand new!', 
          'Super high quality display great for watching sport and movies. Really vivid colours and 5 star reviews all across the web. Get yours now!',
          '2020-11-04T17:36:01', '2020-11-04T17:36:01', '2020-11-05T17:36:01', 150.00, 300.00, 10.00, 2, 3);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Men''s Barbour Jacket with some small scratches. Size Large.', 
          'Classic Barbour jacket, ideal for your loved one. Selling as I''m moving abroad to a tropical paradise. Minor wear and tear both nothing too serious.',
          '2020-11-03T09:31:45', '2020-11-03T09:31:45', '2020-11-04T09:31:45', 40.00, 40.00, 1.00, 3, 4);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Ladies Peep toe Stiletto Shoes Evening High Heels Ankle strap Sandals size 3-8', 
          'Approx heel height of 4.5" and platform height of 1". Faux suede material and true to size. Brand new and in the box.',
          '2020-11-02T20:54:08', '2020-11-02T20:54:08', '2020-11-03T20:54:08', 19.95, 30.00, 0.50, 4, 4);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Rolex Tudor Prince Oysterdate 9050/0 Automatic 1978', 
          'Rolex Tudor Automatic 1978 Oysterdate 9050/0. Here we have a beautiful Rolex Tudor Automatic Oysterdate 9050/0 from 1978. 34mm Oyster case. Acrylic crystal. Date feature. Quick set movement.',
          '2020-11-01T23:59:59', '2020-11-01T23:59:59', '2020-11-03T23:59:59', 975.00, 1200.00, 10.00, 5, 6);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Used Aston Martin DB9 - the car driven by 007! Avimore Blue with alloy wheels.', 
          'Thank you for looking at my wonderful DB9. This is my second DB9, the first was many years ago but not a volante and I always had a hankering for one. This car stood out because of the colour, there are not many that you see in this Avimore Blue which really sets the car off.',
          '2020-10-31T12:01:21', '2020-10-31T12:01:21', '2020-11-02T12:01:21', 29999.00, 35000.00, 1000.00, 6, 7);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Kids jungle gym climbing frame set ideal for gardens with limited space', 
          'Has 2 towers. Bridge between them. 2 slides, 2 access ladders, climbing wall access, rope access, rope ladder access. 2 swing position currently 1 swing and 1 pull up but have the other swing the rope just broke. I also have the 2 canopies for the top but the rods broke so just needs some simple supports. Been amazing for my children but time for it to go to a new home. Was almost £2k new.',
          '2020-10-30T14:00:00', '2020-10-30T14:00:00', '2020-11-01T14:00:00', 50.00, 75.00, 0.01, 7, 8);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Head "Gravity" tennis racquet - barely used', 
          'Head Gravity Mp Tennis Racket in excellent condition. Grip size 2. Strung with rpm bast / syn gut hybrid.',
          '2020-10-28T15:36:29', '2020-10-29T15:36:29', '2020-11-01T15:36:29', 60.00, 75.00, 2.00, 8, 8);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('BRAND NEW! Database Systems; A Practical Approach to Design, Implementation, and Management (6ed)', 
          'Recommended for any serious student of databases. 1400 (!) pages of great information present in a coherent and easy-to-understand way.',
          '2020-11-06T12:00:00', '2020-11-06T12:00:00', '2020-11-10T12:00:00', 100.00, 110.00, 1.00, 1, 10);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('DLux Vitamin D daily oral spray', 
          'Great for use in the winter months when you aren''t getting enough sun. Especially important at the moment..!',
          '2020-11-05T14:29:31', '2020-11-05T14:29:31', '2020-11-11T14:29:31', 5.00, 5.00, 0.01, 1, 11);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('VariDesk ProPlus36 Standing Desk Converter Unit for Dual Monitors IN WHITE', 
          'This is for a varidesk proplus36 standing desk converter unit for dual monitors in white. It is in great condition and can be viewed or collected in Wellington, Somerset. I will post if needed too.',
          '2020-11-04T09:28:28', '2020-11-04T09:28:28', '2020-11-12T09:28:28', 82.00, 100.00, 1.00, 2, 13);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Yamaha 61 Key Electric Digital Piano Musical Beginner Electronic Keyboard Instrument', 
          'This electric digital piano has 61 key standard keys and 128 rhythms tones, which gives you wonderful sound quality. There are 5 functions especially tailored for beginners.',
          '2020-11-03T13:13:24', '2020-11-03T13:13:24', '2020-11-13T13:13:24', 3.00, 5.00, 0.01, 3, 12);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Skydiving experience for two', 
          'Go tandem skydiving in beautiful Gloucestershire and have an adrenaline rush like you''ve never experienced before! Only one jump per person per experience.',
          '2020-11-02T18:00:30', '2020-11-02T18:00:30', '2020-11-14T18:00:30', 250.00, 250.00, 5.00, 4, 1);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('1000 Different Worldwide Stamps Collection', 
          'Some stamp packets may occasionally contain some CTO stamps if they have been issued like this by the relevant post office. In some cases larger stamp packets may contain variations of the same stamp, these are usually different perforations or watermarks.',
          '2020-11-01T22:14:29', '2020-11-01T22:14:29', '2020-11-15T22:14:29', 10.00, 15.00, 1.00, 5, 2);
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ('Gold-coloured necklace 39cm', 
          'Pretty gold-coloured necklace 39cm (looks like gold but no mark as far as I can see). Condition is "Used" but good. Clasp in working order.',
          '2020-10-29T18:10:11', '2020-11-01T18:10:11', '2020-11-16T18:10:11', 0.99, 0.99, 0.01, 6, 6);

-- Bids x30         
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-05T13:24:13', 3.00, 1, 1);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-05T15:29:23', 4.00, 2, 1);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-06T09:11:21', 5.00, 1, 1);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-06T11:30:54', 5.01, 2, 1);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-04T19:32:29', 150.00, 5, 2);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-04T21:20:11', 160.00, 10, 2);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-05T08:00:48', 250.00, 4, 2);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-05T12:00:01', 340.00, 3, 2);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-03T09:45:41', 40.00, 8, 3);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-04T03:12:22', 48.00, 2, 3);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-04T09:30:11', 49.00, 9, 3);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-04T09:31:42', 50.00, 6, 3);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-02T22:51:19', 19.95, 10, 4);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-03T16:45:01', 20.00, 7, 4);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-03T19:11:00', 31.00, 8, 4);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-02T01:03:45', 975.00, 5, 5);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-03T15:14:29', 1400.00, 8, 5);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-03T23:58:34', 1450.00, 9, 5);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-10-31T13:33:20', 32000.00, 7, 6);  
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-10-30T15:01:22', 50.00, 8, 7);  
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-10-30T18:29:43', 65.00, 4, 7);  
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-06T16:29:53', 100.00, 2, 9); 
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-06T17:31:20', 115.00, 7, 9);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-09T11:34:59', 120.00, 4, 9);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-10T11:59:03', 121.00, 5, 9);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-07T03:09:56', 5.00, 6, 10);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-09T01:24:00', 6.00, 7, 10);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-05T11:31:04', 82.00, 7, 11);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-03T20:18:21', 3.00, 5, 12);
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('2020-11-04T18:54:09', 250.00, 3, 13);

-- Watching x20
INSERT INTO Watching (buyerID, auctionID) VALUES (1, 3);
INSERT INTO Watching (buyerID, auctionID) VALUES (1, 4);
INSERT INTO Watching (buyerID, auctionID) VALUES (2, 3);
INSERT INTO Watching (buyerID, auctionID) VALUES (2, 11);
INSERT INTO Watching (buyerID, auctionID) VALUES (3, 8);
INSERT INTO Watching (buyerID, auctionID) VALUES (9, 1);
INSERT INTO Watching (buyerID, auctionID) VALUES (10, 10);
INSERT INTO Watching (buyerID, auctionID) VALUES (7, 4);
INSERT INTO Watching (buyerID, auctionID) VALUES (3, 2);
INSERT INTO Watching (buyerID, auctionID) VALUES (8, 7);
INSERT INTO Watching (buyerID, auctionID) VALUES (4, 3);
INSERT INTO Watching (buyerID, auctionID) VALUES (4, 4);
INSERT INTO Watching (buyerID, auctionID) VALUES (4, 2);
INSERT INTO Watching (buyerID, auctionID) VALUES (9, 15);
INSERT INTO Watching (buyerID, auctionID) VALUES (10, 12);
INSERT INTO Watching (buyerID, auctionID) VALUES (3, 14);
INSERT INTO Watching (buyerID, auctionID) VALUES (1, 8);
INSERT INTO Watching (buyerID, auctionID) VALUES (9, 7);
INSERT INTO Watching (buyerID, auctionID) VALUES (8, 15);
INSERT INTO Watching (buyerID, auctionID) VALUES (7, 15);

-- Categories x12 (in addition to 'Other' created for the Defaults table)
INSERT INTO Categories (categoryName) VALUES ('Collectables & antiques');
INSERT INTO Categories (categoryName) VALUES ('Electronics');
INSERT INTO Categories (categoryName) VALUES ('Fashion');
INSERT INTO Categories (categoryName) VALUES ('Home & garden');
INSERT INTO Categories (categoryName) VALUES ('Jewellery & watches');
INSERT INTO Categories (categoryName) VALUES ('Motors');
INSERT INTO Categories (categoryName) VALUES ('Sporting goods');
INSERT INTO Categories (categoryName) VALUES ('Toys & games');
INSERT INTO Categories (categoryName) VALUES ('Books, comics & magazines');
INSERT INTO Categories (categoryName) VALUES ('Health & beauty');
INSERT INTO Categories (categoryName) VALUES ('Musical instruments');
INSERT INTO Categories (categoryName) VALUES ('Business, office & industrial');

-- Countries x195
INSERT INTO Countries (countryName) VALUES ('Afghanistan');
INSERT INTO Countries (countryName) VALUES ('Albania');
INSERT INTO Countries (countryName) VALUES ('Algeria');
INSERT INTO Countries (countryName) VALUES ('Andorra');
INSERT INTO Countries (countryName) VALUES ('Angola');
INSERT INTO Countries (countryName) VALUES ('Antigua and Barbuda');
INSERT INTO Countries (countryName) VALUES ('Argentina');
INSERT INTO Countries (countryName) VALUES ('Armenia');
INSERT INTO Countries (countryName) VALUES ('Australia');
INSERT INTO Countries (countryName) VALUES ('Austria');
INSERT INTO Countries (countryName) VALUES ('Azerbaijan');
INSERT INTO Countries (countryName) VALUES ('Bahamas');
INSERT INTO Countries (countryName) VALUES ('Bahrain');
INSERT INTO Countries (countryName) VALUES ('Bangladesh');
INSERT INTO Countries (countryName) VALUES ('Barbados');
INSERT INTO Countries (countryName) VALUES ('Belarus');
INSERT INTO Countries (countryName) VALUES ('Belgium');
INSERT INTO Countries (countryName) VALUES ('Belize');
INSERT INTO Countries (countryName) VALUES ('Benin');
INSERT INTO Countries (countryName) VALUES ('Bhutan');
INSERT INTO Countries (countryName) VALUES ('Bolivia');
INSERT INTO Countries (countryName) VALUES ('Bosnia and Herzegovina');
INSERT INTO Countries (countryName) VALUES ('Botswana');
INSERT INTO Countries (countryName) VALUES ('Brazil');
INSERT INTO Countries (countryName) VALUES ('Brunei');
INSERT INTO Countries (countryName) VALUES ('Bulgaria');
INSERT INTO Countries (countryName) VALUES ('Burkina Faso');
INSERT INTO Countries (countryName) VALUES ('Burundi');
INSERT INTO Countries (countryName) VALUES ('Côte d''Ivoire');
INSERT INTO Countries (countryName) VALUES ('Cabo Verde');
INSERT INTO Countries (countryName) VALUES ('Cambodia');
INSERT INTO Countries (countryName) VALUES ('Cameroon');
INSERT INTO Countries (countryName) VALUES ('Canada');
INSERT INTO Countries (countryName) VALUES ('Central African Republic');
INSERT INTO Countries (countryName) VALUES ('Chad');
INSERT INTO Countries (countryName) VALUES ('Chile');
INSERT INTO Countries (countryName) VALUES ('China');
INSERT INTO Countries (countryName) VALUES ('Colombia');
INSERT INTO Countries (countryName) VALUES ('Comoros');
INSERT INTO Countries (countryName) VALUES ('Congo (Congo-Brazzaville)');
INSERT INTO Countries (countryName) VALUES ('Costa Rica');
INSERT INTO Countries (countryName) VALUES ('Croatia');
INSERT INTO Countries (countryName) VALUES ('Cuba');
INSERT INTO Countries (countryName) VALUES ('Cyprus');
INSERT INTO Countries (countryName) VALUES ('Czechia (Czech Republic)');
INSERT INTO Countries (countryName) VALUES ('Democratic Republic of the Congo');
INSERT INTO Countries (countryName) VALUES ('Denmark');
INSERT INTO Countries (countryName) VALUES ('Djibouti');
INSERT INTO Countries (countryName) VALUES ('Dominica');
INSERT INTO Countries (countryName) VALUES ('Dominican Republic');
INSERT INTO Countries (countryName) VALUES ('Ecuador');
INSERT INTO Countries (countryName) VALUES ('Egypt');
INSERT INTO Countries (countryName) VALUES ('El Salvador');
INSERT INTO Countries (countryName) VALUES ('Equatorial Guinea');
INSERT INTO Countries (countryName) VALUES ('Eritrea');
INSERT INTO Countries (countryName) VALUES ('Estonia');
INSERT INTO Countries (countryName) VALUES ('Eswatini (fmr. "Swaziland")');
INSERT INTO Countries (countryName) VALUES ('Ethiopia');
INSERT INTO Countries (countryName) VALUES ('Fiji');
INSERT INTO Countries (countryName) VALUES ('Finland');
INSERT INTO Countries (countryName) VALUES ('France');
INSERT INTO Countries (countryName) VALUES ('Gabon');
INSERT INTO Countries (countryName) VALUES ('Gambia');
INSERT INTO Countries (countryName) VALUES ('Georgia');
INSERT INTO Countries (countryName) VALUES ('Germany');
INSERT INTO Countries (countryName) VALUES ('Ghana');
INSERT INTO Countries (countryName) VALUES ('Greece');
INSERT INTO Countries (countryName) VALUES ('Grenada');
INSERT INTO Countries (countryName) VALUES ('Guatemala');
INSERT INTO Countries (countryName) VALUES ('Guinea');
INSERT INTO Countries (countryName) VALUES ('Guinea-Bissau');
INSERT INTO Countries (countryName) VALUES ('Guyana');
INSERT INTO Countries (countryName) VALUES ('Haiti');
INSERT INTO Countries (countryName) VALUES ('Holy See');
INSERT INTO Countries (countryName) VALUES ('Honduras');
INSERT INTO Countries (countryName) VALUES ('Hungary');
INSERT INTO Countries (countryName) VALUES ('Iceland');
INSERT INTO Countries (countryName) VALUES ('India');
INSERT INTO Countries (countryName) VALUES ('Indonesia');
INSERT INTO Countries (countryName) VALUES ('Iran');
INSERT INTO Countries (countryName) VALUES ('Iraq');
INSERT INTO Countries (countryName) VALUES ('Ireland');
INSERT INTO Countries (countryName) VALUES ('Israel');
INSERT INTO Countries (countryName) VALUES ('Italy');
INSERT INTO Countries (countryName) VALUES ('Jamaica');
INSERT INTO Countries (countryName) VALUES ('Japan');
INSERT INTO Countries (countryName) VALUES ('Jordan');
INSERT INTO Countries (countryName) VALUES ('Kazakhstan');
INSERT INTO Countries (countryName) VALUES ('Kenya');
INSERT INTO Countries (countryName) VALUES ('Kiribati');
INSERT INTO Countries (countryName) VALUES ('Kuwait');
INSERT INTO Countries (countryName) VALUES ('Kyrgyzstan');
INSERT INTO Countries (countryName) VALUES ('Laos');
INSERT INTO Countries (countryName) VALUES ('Latvia');
INSERT INTO Countries (countryName) VALUES ('Lebanon');
INSERT INTO Countries (countryName) VALUES ('Lesotho');
INSERT INTO Countries (countryName) VALUES ('Liberia');
INSERT INTO Countries (countryName) VALUES ('Libya');
INSERT INTO Countries (countryName) VALUES ('Liechtenstein');
INSERT INTO Countries (countryName) VALUES ('Lithuania');
INSERT INTO Countries (countryName) VALUES ('Luxembourg');
INSERT INTO Countries (countryName) VALUES ('Madagascar');
INSERT INTO Countries (countryName) VALUES ('Malawi');
INSERT INTO Countries (countryName) VALUES ('Malaysia');
INSERT INTO Countries (countryName) VALUES ('Maldives');
INSERT INTO Countries (countryName) VALUES ('Mali');
INSERT INTO Countries (countryName) VALUES ('Malta');
INSERT INTO Countries (countryName) VALUES ('Marshall Islands');
INSERT INTO Countries (countryName) VALUES ('Mauritania');
INSERT INTO Countries (countryName) VALUES ('Mauritius');
INSERT INTO Countries (countryName) VALUES ('Mexico');
INSERT INTO Countries (countryName) VALUES ('Micronesia');
INSERT INTO Countries (countryName) VALUES ('Moldova');
INSERT INTO Countries (countryName) VALUES ('Monaco');
INSERT INTO Countries (countryName) VALUES ('Mongolia');
INSERT INTO Countries (countryName) VALUES ('Montenegro');
INSERT INTO Countries (countryName) VALUES ('Morocco');
INSERT INTO Countries (countryName) VALUES ('Mozambique');
INSERT INTO Countries (countryName) VALUES ('Myanmar (formerly Burma)');
INSERT INTO Countries (countryName) VALUES ('Namibia');
INSERT INTO Countries (countryName) VALUES ('Nauru');
INSERT INTO Countries (countryName) VALUES ('Nepal');
INSERT INTO Countries (countryName) VALUES ('Netherlands');
INSERT INTO Countries (countryName) VALUES ('New Zealand');
INSERT INTO Countries (countryName) VALUES ('Nicaragua');
INSERT INTO Countries (countryName) VALUES ('Niger');
INSERT INTO Countries (countryName) VALUES ('Nigeria');
INSERT INTO Countries (countryName) VALUES ('North Korea');
INSERT INTO Countries (countryName) VALUES ('North Macedonia');
INSERT INTO Countries (countryName) VALUES ('Norway');
INSERT INTO Countries (countryName) VALUES ('Oman');
INSERT INTO Countries (countryName) VALUES ('Pakistan');
INSERT INTO Countries (countryName) VALUES ('Palau');
INSERT INTO Countries (countryName) VALUES ('Palestine State');
INSERT INTO Countries (countryName) VALUES ('Panama');
INSERT INTO Countries (countryName) VALUES ('Papua New Guinea');
INSERT INTO Countries (countryName) VALUES ('Paraguay');
INSERT INTO Countries (countryName) VALUES ('Peru');
INSERT INTO Countries (countryName) VALUES ('Philippines');
INSERT INTO Countries (countryName) VALUES ('Poland');
INSERT INTO Countries (countryName) VALUES ('Portugal');
INSERT INTO Countries (countryName) VALUES ('Qatar');
INSERT INTO Countries (countryName) VALUES ('Romania');
INSERT INTO Countries (countryName) VALUES ('Russia');
INSERT INTO Countries (countryName) VALUES ('Rwanda');
INSERT INTO Countries (countryName) VALUES ('Saint Kitts and Nevis');
INSERT INTO Countries (countryName) VALUES ('Saint Lucia');
INSERT INTO Countries (countryName) VALUES ('Saint Vincent and the Grenadines');
INSERT INTO Countries (countryName) VALUES ('Samoa');
INSERT INTO Countries (countryName) VALUES ('San Marino');
INSERT INTO Countries (countryName) VALUES ('Sao Tome and Principe');
INSERT INTO Countries (countryName) VALUES ('Saudi Arabia');
INSERT INTO Countries (countryName) VALUES ('Senegal');
INSERT INTO Countries (countryName) VALUES ('Serbia');
INSERT INTO Countries (countryName) VALUES ('Seychelles');
INSERT INTO Countries (countryName) VALUES ('Sierra Leone');
INSERT INTO Countries (countryName) VALUES ('Singapore');
INSERT INTO Countries (countryName) VALUES ('Slovakia');
INSERT INTO Countries (countryName) VALUES ('Slovenia');
INSERT INTO Countries (countryName) VALUES ('Solomon Islands');
INSERT INTO Countries (countryName) VALUES ('Somalia');
INSERT INTO Countries (countryName) VALUES ('South Africa');
INSERT INTO Countries (countryName) VALUES ('South Korea');
INSERT INTO Countries (countryName) VALUES ('South Sudan');
INSERT INTO Countries (countryName) VALUES ('Spain');
INSERT INTO Countries (countryName) VALUES ('Sri Lanka');
INSERT INTO Countries (countryName) VALUES ('Sudan');
INSERT INTO Countries (countryName) VALUES ('Suriname');
INSERT INTO Countries (countryName) VALUES ('Sweden');
INSERT INTO Countries (countryName) VALUES ('Switzerland');
INSERT INTO Countries (countryName) VALUES ('Syria');
INSERT INTO Countries (countryName) VALUES ('Tajikistan');
INSERT INTO Countries (countryName) VALUES ('Tanzania');
INSERT INTO Countries (countryName) VALUES ('Thailand');
INSERT INTO Countries (countryName) VALUES ('Timor-Leste');
INSERT INTO Countries (countryName) VALUES ('Togo');
INSERT INTO Countries (countryName) VALUES ('Tonga');
INSERT INTO Countries (countryName) VALUES ('Trinidad and Tobago');
INSERT INTO Countries (countryName) VALUES ('Tunisia');
INSERT INTO Countries (countryName) VALUES ('Turkey');
INSERT INTO Countries (countryName) VALUES ('Turkmenistan');
INSERT INTO Countries (countryName) VALUES ('Tuvalu');
INSERT INTO Countries (countryName) VALUES ('Uganda');
INSERT INTO Countries (countryName) VALUES ('Ukraine');
INSERT INTO Countries (countryName) VALUES ('United Arab Emirates');
INSERT INTO Countries (countryName) VALUES ('United Kingdom');
INSERT INTO Countries (countryName) VALUES ('United States of America');
INSERT INTO Countries (countryName) VALUES ('Uruguay');
INSERT INTO Countries (countryName) VALUES ('Uzbekistan');
INSERT INTO Countries (countryName) VALUES ('Vanuatu');
INSERT INTO Countries (countryName) VALUES ('Venezuela');
INSERT INTO Countries (countryName) VALUES ('Vietnam');
INSERT INTO Countries (countryName) VALUES ('Yemen');
INSERT INTO Countries (countryName) VALUES ('Zambia');
INSERT INTO Countries (countryName) VALUES ('Zimbabwe');

-- Re-enable FK checks when importing
SET FOREIGN_KEY_CHECKS = 1;


/* 
*** End of script ***
*/