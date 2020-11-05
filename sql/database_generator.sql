/* 
*** freeBay DB generation script incl. sample data ***
*/

-- Drop previous copies of DB if exist
DROP DATABASE Freebay;

-- Create new DB & grant privileges to admin
CREATE DATABASE Freebay
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

GRANT SELECT, UPDATE, INSERT, DELETE
    ON Freebay.*
    TO 'admin'@'localhost'
    IDENTIFIED BY '#f6@Q-hCZv';

-- Select correct DB for use 
USE Freebay;

-- Table creation (x7, excl. FKs)
CREATE TABLE Buyers (
    buyerID INT UNSIGNED AUTO_INCREMENT 
    username VARCHAR(20) NOT NULL
    email VARCHAR(254) NOT NULL
    pass CHAR(60) NOT NULL
    firstName VARCHAR(35) NOT NULL
    familyName VARCHAR(35) NOT NULL
    telNo BIGINT UNSIGNED   -- Null allowed (optional user input)
    addressID INT UNSIGNED  -- Null allowed (optional user input)
    CONSTRAINT Buyers_pk PRIMARY KEY (buyerID)
    CONSTRAINT Buyers_ck UNIQUE (username)
);

CREATE TABLE Sellers (
    sellerID INT UNSIGNED AUTO_INCREMENT
    username VARCHAR(20) NOT NULL
    email VARCHAR(254) NOT NULL
    passwrd CHAR(60) NOT NULL
    firstName VARCHAR(35) NOT NULL
    familyName VARCHAR(35) NOT NULL
    telNo BIGINT UNSIGNED   -- Null allowed (optional user input)
    addressID INT UNSIGNED  -- Null allowed (optional user input)
    CONSTRAINT Sellers_pk PRIMARY KEY (sellerID)
    CONSTRAINT Sellers_ck UNIQUE (username)
);

CREATE TABLE Addresses (
    addressID INT UNSIGNED AUTO_INCREMENT
    line1 VARCHAR(35) NOT NULL
    city VARCHAR(35) NOT NULL
    postcode VARCHAR(35) NOT NULL
    countryID TINYINT UNSIGNED
    CONSTRAINT Addresses_pk PRIMARY KEY (addressID)
);

CREATE TABLE Countries (
    countryID TINYINT UNSIGNED AUTO_INCREMENT
    countryName VARCHAR(35) NOT NULL
    CONSTRAINT Countries_pk PRIMARY KEY (countryID)
    CONSTRAINT Countries_ck UNIQUE (countryName)
)

CREATE TABLE Bids (
    bidID BIGINT UNSIGNED AUTO_INCREMENT
    bidDate DATETIME NOT NULL
    bidAmount DECIMAL(9,2) UNSIGNED NOT NULL
    buyerID INT UNSIGNED NOT NULL DEFAULT 1
    auctionID BIGINT UNSIGNED NOT NULL
    CONSTRAINT Bids_pk PRIMARY KEY (bidID)
);

CREATE TABLE Watching (
    buyerID INT UNSIGNED 
    auctionID BIGINT UNSIGNED
    CONSTRAINT Watching_pk PRIMARY KEY (buyerID, auctionID)
);

CREATE TABLE Auctions (
    auctionID BIGINT UNSIGNED AUTO_INCREMENT
    title VARCHAR(80) NOT NULL
    descript VARCHAR(4000) NOT NULL
    createDate DATETIME NOT NULL
    startDate DATETIME NOT NULL
    endDate DATETIME NOT NULL
    startPrice DECIMAL(9,2) UNSIGNED NOT NULL
    reservePrice DECIMAL(9,2) UNSIGNED  -- Null allowed (optional user input)
    minIncrement DECIMAL(8,2) UNSIGNED  -- Null allowed (optional user input)
    sellerID INT UNSIGNED NOT NULL DEFAULT 1               
    categoryID TINYINT UNSIGNED NOT NULL DEFAULT 1     
    CONSTRAINT Auctions_pk PRIMARY KEY (auctionID)
);

CREATE TABLE Categories (
    categoryID TINYINT UNSIGNED AUTO_INCREMENT
    categoryName VARCHAR(35) NOT NULL
    CONSTRAINT Categories_pk PRIMARY KEY (categoryID)
    CONSTRAINT Categories_ck UNIQUE (categoryName)
);

-- Adding FKs and referential integrity constraints (ON DELETE, ON UPDATE) in 3 groups:

-- Group 1: ON DELETE, SET NULL (data the FK is referencing is optional for users on sign-up; full discussion in report)
ALTER TABLE Buyers ADD CONSTRAINT Buyers_fk 
  FOREIGN KEY (addressID) 
  REFERENCES Addresses(addressID)
  ON UPDATE CASCADE
  ON DELETE SET NULL;

ALTER TABLE Sellers ADD CONSTRAINT Sellers_fk 
  FOREIGN KEY (addressID) 
  REFERENCES Addresses(addressID)
  ON UPDATE CASCADE
  ON DELETE SET NULL;

-- Group 2: ON DELETE, SET DEFAULT (allows column to maintain no nulls; full discussion in report)
ALTER TABLE Bids ADD CONSTRAINT Bids_fk1 
  FOREIGN KEY (buyerID) 
  REFERENCES Buyers(buyerID)
  ON UPDATE CASCADE
  ON DELETE SET DEFAULT;

ALTER TABLE Auctions ADD CONSTRAINT Auctions_fk1 
  FOREIGN KEY (sellerID) 
  REFERENCES Sellers(sellerID)
  ON UPDATE CASCADE
  ON DELETE SET DEFAULT;

ALTER TABLE Auctions ADD CONSTRAINT Auctions_fk2 
  FOREIGN KEY (categoryID) 
  REFERENCES Categories(categoryID)
  ON UPDATE CASCADE
  ON DELETE SET DEFAULT;

-- Group 3: ON DELETE, CASCADE (information is no longer relevant without parent entity; full discussion in report)
ALTER TABLE Bids ADD CONSTRAINT Bids_fk2 
  FOREIGN KEY (auctionID) 
  REFERENCES Auctions(auctionID)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Watching ADD CONSTRAINT Watching_fk1 
  FOREIGN KEY (buyerID) 
  REFERENCES Buyers(buyerID)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Watching ADD CONSTRAINT Watching_fk2 
  FOREIGN KEY (auctionID) 
  REFERENCES Auctions(auctionID)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

-- Create a 'Defaults' table referencing Group 2 parent tables' PKs with a FK constraint to prevent deletion of default values 

CREATE TABLE Defaults (
    buyerID INT UNSIGNED
    sellerID INT UNSIGNED
    categoryID TINYINT UNSIGNED
    FOREIGN KEY (buyerID)
      REFERENCES Buyers(buyerID)
    FOREIGN KEY (sellerID)
      REFERENCES Sellers(sellerID)
    FOREIGN KEY (categoryID)
      REFERENCES Categories(categoryID)
);

INSERT INTO Buyers (username, email, pass, firstName, familyName)
  VALUES ('N/A', 'N/A', '000000000000000000000000000000000000000000000000000000000000', 'N/A', 'N/A');
INSERT INTO Sellers (username, email, pass, firstName, familyName)
  VALUES ('N/A', 'N/A', '000000000000000000000000000000000000000000000000000000000000', 'N/A', 'N/A');
INSERT INTO Categories (categoryName) VALUES ('Other');
INSERT INTO Defaults (buyerID, sellerID, categoryID) VALUES (1, 1, 1);

-- Sample data

-- Buyers x10 (in addition to the default buyer created for the Defaults table)
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('requirehaggard', 'pajas@optonline.net', 'Callie', 'Christie', 12025550169, 1);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('suitgrin', 'skajan@icloud.com', 'Nela', 'Beil', 12025550119, 2);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('educatorlyrical', 'msloan@msn.com', 'Alys', 'Hughes', 12025550106, 3);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('hardballtalented', 'quinn@hotmail.com', 'Abbas', 'Mcdermott', 12025550141, 4);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('unluckyresource', 'tarreau@live.com', 'Kayla', 'Hurley', 12025550113, 5);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('bowspritcalling', 'tarreau@live.com', 'Meadow', 'Suarez', 12025550172, 6);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('drabitbedight', 'qmacro@optonline.net', 'Rebecca', 'Quintero', 441632960259, 7);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('cheelpervous', 'wiseb@hotmail.com', 'Jayden', 'Emerson', 441632960095, 7);
INSERT INTO Buyers (username, email, pass, firstName, familyName, telNo)
  VALUES ('utterwhaffle', 'greear@att.net', 'Roxy', 'Garrett', 441632960947);
INSERT INTO Buyers (username, email, pass, firstName, familyName, addressID)
  VALUES ('exultantwithers', 'twoflower@comcast.net', 'Katelyn', 'Gordon', 8);

-- Sellers x10 (in addition to the default seller created for the Defaults table)
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('rulesuper', 'carcus@optonline.net', 'Jaxon', 'Daniels', 441632960541, 9);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('coastfoking', 'mdielmann@optonline.net', 'Nojus', 'Grainger', 441632960324, 10);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('scourgetrotwood', 'mallanmba@optonline.net', 'Shantelle', 'Stevenson', 441632960259, 11);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('countmatter', 'jcholewa@verizon.net', 'Beatrice', 'Mcmanus', 81759267428, 12);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('politicsduro', 'bester@gmail.com', 'Herman', 'Gunn', 81753771329, 13);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('sensualbeginner', 'msusa@gmail.com', 'Olivia-Rose', 'Handley', 81752700372, 14);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('vardknobby', 'neuffer@me.com', 'Rachel', 'Rangel', 81755858066, 15);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo, addressID)
  VALUES ('quoteelectronic', 'houle@sbcglobal.net', 'Carwyn', 'Mendez', 81755473558, 15);
INSERT INTO Sellers (username, email, pass, firstName, familyName, telNo)
  VALUES ('gashfordmocolate', 'sonnen@me.com', 'Regina', 'Mccabe', 81755473558);
INSERT INTO Sellers (username, email, pass, firstName, familyName, addressID)
  VALUES ('rovelyeregion', 'mglee@yahoo.ca', 'Moesha', 'Gallagher', 16);

-- Addresses x16
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ('216 North High Point Street', 'Beverly', '01915', );
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();
INSERT INTO Addresses (line1, city, postcode, countryID) VALUES ();

-- Bids x30
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();
INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ();

-- Watching x20
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();
INSERT INTO Watching (buyerID, auctionID) VALUES ();

-- Auctions x15
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();
INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID)
  VALUES ();

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


/* 
*** End of script ***
*/