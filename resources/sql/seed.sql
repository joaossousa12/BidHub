CREATE SCHEMA IF NOT EXISTS lbaw2364;

SET search_path TO lbaw2364;

-- Drop tables

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS auction_state CASCADE;
DROP TABLE IF EXISTS auction CASCADE;
DROP TABLE IF EXISTS sent CASCADE;
DROP TABLE IF EXISTS category CASCADE;
DROP TABLE IF EXISTS belongs CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS auctionModification CASCADE;
DROP TABLE IF EXISTS approvedAuction CASCADE;
DROP TABLE IF EXISTS removedAuction CASCADE;
DROP TABLE IF EXISTS bid CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS faqs CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;

-- Tables

-- user
CREATE TABLE users(
    id SERIAL,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    address TEXT NOT NULL,
    postalCode TEXT NOT NULL,
    phoneNumber TEXT NOT NULL UNIQUE,
    credit FLOAT DEFAULT 0,
    average_rating NUMERIC(3, 2) DEFAULT NULL,
    rating_count INTEGER DEFAULT 0,
    is_admin BOOLEAN NOT NULL,
    is_deleted BOOLEAN NOT NULL,
    profile_picture VARCHAR,
    is_banned BOOLEAN NOT NULL,
    remember_token VARCHAR,
    PRIMARY KEY(id)
);


-- auction state
CREATE TABLE auction_state (
    id SERIAL ,
    state_name VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY(id)
);

-- auction
CREATE TABLE auction(
    id SERIAL,
    title TEXT NOT NULL,
    description TEXT DEFAULT 'No Description',
    dateCreated TIMESTAMP WITH TIME ZONE NOT NULL,
    duration FLOAT NOT NULL,
    minValue NUMERIC NOT NULL,
    starting_value NUMERIC,
    owner_id INTEGER REFERENCES users,
    state_id INTEGER  REFERENCES auction_state,
    --CONSTRAINT dateCreated_ck CHECK (dateCreated <= CURRENT_DATE),
    PRIMARY KEY(id)
);

-- notification
CREATE TABLE notification(
    id SERIAL,
    date TIMESTAMP WITH TIME ZONE,
    information TEXT NOT NULL,
    viewed BOOLEAN DEFAULT FALSE,
    user_id INTEGER REFERENCES users,
    auction_id INTEGER REFERENCES auction,
    PRIMARY KEY(id)
);


-- sent
CREATE TABLE sent(
    notification_id INTEGER REFERENCES notification,
    auction_id INTEGER REFERENCES auction,
    PRIMARY KEY(auction_id, notification_id)
);

-- category
CREATE TABLE category(
    categoryName TEXT NOT NULL,
    PRIMARY KEY(categoryName)
);

--follows
CREATE TABLE follows(
    buyer_id INTEGER REFERENCES users,
    auction_id INTEGER REFERENCES auction,
    PRIMARY KEY (buyer_id, auction_id)

);

-- belongs
CREATE TABLE belongs(
    auction_id INTEGER REFERENCES auction,
    categoryName TEXT REFERENCES category,
    PRIMARY KEY(auction_id, categoryName)
);

-- auctionModification
CREATE TABLE auctionModification(
    id SERIAL,
    dateRequest TIMESTAMP WITH TIME ZONE NOT NULL,
    newDescription TEXT NOT NULL,
    approvedDate TIMESTAMP WITH TIME ZONE NOT NULL,
    auction_id INTEGER REFERENCES auction,
    PRIMARY KEY(id)
);

-- approvedAuction
CREATE TABLE approvedAuction(
    auction_id INTEGER REFERENCES auction,
    dateApproved TIMESTAMP WITH TIME ZONE NOT NULL,
    dateFinished TIMESTAMP WITH TIME ZONE NOT NULL,
    PRIMARY KEY(auction_id)
);

-- removedAuction
CREATE TABLE removedAuction(
    auction_id INTEGER REFERENCES auction,
    dateRemoved TIMESTAMP WITH TIME ZONE NOT NULL,
    PRIMARY KEY(auction_id)
);

-- bid
CREATE TABLE bid(
    auction_id INTEGER REFERENCES auction,
    bidder INTEGER REFERENCES users,
    bidDate TIMESTAMP WITH TIME ZONE NOT NULL,
    bidValue NUMERIC NOT NULL,
    winner BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY(auction_id, bidder)
);

-- faq
CREATE TABLE faqs(
    id SERIAL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE password_reset_tokens (
    email TEXT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);


-- INDEXES

CREATE INDEX idx_auction_dateCreated ON auction (dateCreated);

-- FTS INDEXES

ALTER TABLE auction ADD COLUMN tsvectors TSVECTOR;

CREATE OR REPLACE FUNCTION auction_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = setweight(to_tsvector('english', NEW.title), 'A');
    ELSIF TG_OP = 'UPDATE' THEN
        IF NEW.title <> OLD.title THEN
            NEW.tsvectors = setweight(to_tsvector('english', NEW.title), 'A');
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER auction_search_update
    BEFORE INSERT OR UPDATE ON auction
    FOR EACH ROW
    EXECUTE FUNCTION auction_search_update();

CREATE INDEX idx_auction_search ON auction USING GIN(tsvectors);

-- Triggers

CREATE OR REPLACE FUNCTION ban_user() RETURNS TRIGGER AS $$
BEGIN
    UPDATE users SET type = 'banned' WHERE users.id = NEW.user_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_ban_user
    BEFORE DELETE ON users
    FOR EACH ROW
    EXECUTE FUNCTION ban_user();

CREATE OR REPLACE FUNCTION prevent_self_bid() RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT owner_id FROM auction WHERE auction.id = NEW.auction_id AND owner_id = NEW.bidder) THEN
        RAISE EXCEPTION 'An auction owner cannot bid on its own auctions.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_prevent_self_bid
    BEFORE INSERT ON bid
    FOR EACH ROW
    EXECUTE FUNCTION prevent_self_bid();


SET search_path TO lbaw2364;

INSERT INTO users (username, email, password, address, postalCode, phoneNumber, is_admin, is_deleted, is_banned)
VALUES
    ('António', 'antonio13@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Rua das Flores', '4000-123', '+351968374465', false, false, false),
    ('José', 'jose2002@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Rua dos castelos', '4403-122', '+351968374463', false, false, false),
    ('Marcelo', 'marcelorebelo@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Avenida José Matos', '4222-112', '+3519683744651', false, false, false),
    ('Ana', 'ana2@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Avenida da Boavista', '4374-132', '+351968374466', false, false, false),
    ('Admin', 'bidhubadmin@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Rua Roberto Frias', '4122-122', '+351921087364', true, false, false),
    ('Guilherme', 'guilherme@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Avenida das Boavistas', '4371-132', '+351968374461', false, false, false),
    ('luis', 'luis@gmail.com', '$2y$10$wF15XFan8ueXU.oVrQj.felRDIRkjNL3Iw1lwOAgw7GQVjcr8SSI2', 'Avenida de Boavista', '4331-132', '+351968174461', false, false, false);


--INSERT INTO notification (information, user_id)
--VALUES
--    ('Your auction has ended!', 2),
--    ('You got outbid!', 3),
--    ('New auctions are available!', 4);

INSERT INTO auction_state (state_name) 
VALUES 
    ('approved'),
    ('waiting'),
    ('denied'),
    ('cancelled');

INSERT INTO auction (title, description, dateCreated, duration, minValue, starting_value, owner_id, state_id)
VALUES
    ('Toyota Corolla', 'Good car from 2003 semi-new!', '2023-10-22', 7, 30000, 30000, 1, 2),
    ('Gaming PC Asus', '2023 pc with all the games!', '2023-10-23', 5, 1000, 1000, 2, 2),
    ('Luxury Watch', 'Rolex wore by a president!', '2023-11-24', 4, 29440, 29440, 1,1 ),
    ('Vintage Bicycle', 'Bicycle from 1990', '2023-11-25', 5, 4090, 4090, 2, 1),
    ('Electric Guitar', 'Rare collectible in great condition!', '2023-11-26', 5, 40280, 40280, 1, 1),
    ('Smartphone', 'High-performance gadget for all your needs!', '2023-11-27', 7, 9710, 9710, 2, 1),
    ('Sports Memorabilia', 'A musician`s dream!', '2023-11-28', 10, 11880, 11880, 1, 1),
    ('Fine Art Painting', 'Picasso art', '2023-11-29', 3, 9200, 9200, 1, 3),
    ('Laptop', 'Latest model with top-notch features!', '2023-11-30', 10, 8040, 8040, 2, 2),
    ('Ps5', 'Latest model with top-notch features!', '2023-12-01', 10, 500, 500, 1, 2),
    ('Calculator Casio', 'High-performance gadget for all your needs!', '2023-12-02', 10, 120, 120, 2, 1);


INSERT INTO category (categoryName)
VALUES
    ('Vehicles'),
    ('PC'),
    ('Phones and small eletronics'),
    ('Watches'),
    ('Music'),
    ('Art'),
    ('Gaming');

INSERT INTO belongs (auction_id, categoryName)
VALUES
    (1, 'Vehicles'),
    (2, 'PC'),
    (3, 'Watches'),
    (4, 'Vehicles'),
    (5, 'Music'),
    (6, 'Phones and small eletronics'),
    (7, 'Music'),
    (8, 'Art'),
    (9, 'PC'),
    (10, 'Gaming'),
    (11, 'Phones and small eletronics');

INSERT INTO auctionModification (dateRequest, newDescription, approvedDate, auction_id)
VALUES
    ('2023-10-25', 'Good car from 2003 semi-new!(only comes with 3 wheels)', '2023-10-26', 1),
    ('2023-10-26', 'Asus pcwith all the games!', '2023-10-27', 2);

INSERT INTO approvedAuction (auction_id, dateApproved, dateFinished)
VALUES
    (1, '2023-10-27', '2023-11-03'),
    (2, '2023-10-28', '2023-11-02');

INSERT INTO removedAuction (auction_id, dateRemoved)
VALUES
    (1, '2023-11-04'),
    (2, '2023-11-05');

INSERT INTO bid (auction_id, bidder, bidDate, bidValue, winner)
VALUES
    (1, 3, '2023-10-29', 120, false),
    (2, 4, '2023-10-30', 160, true),
    (11, 3, '2023-12-02', 122, false);

INSERT INTO faqs (question, answer)
VALUES
    ('What are the payment methods accepted?', 'For our website depending on your country, we can accept credit/debit card, paypal and many other payment methods.'),
    ('What happens after i win an auction?', 'After an auction is won the item is going to be sent as soon as possible to the highest bidder`s address.'),
    ('How can i edit my profile?', 'You can edit your profile easily by going to your profile page and clicking the button that says edit your profile and changing the relevant information you want to edit.');
