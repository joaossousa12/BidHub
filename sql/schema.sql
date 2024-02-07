CREATE SCHEMA IF NOT EXISTS lbaw2364;

SET search_path TO lbaw2364;

-- Drop tables

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS auction CASCADE;
DROP TABLE IF EXISTS sent CASCADE;
DROP TABLE IF EXISTS category CASCADE;
DROP TABLE IF EXISTS belongs CASCADE;
DROP TABLE IF EXISTS auctionModification CASCADE;
DROP TABLE IF EXISTS administrator CASCADE;
DROP TABLE IF EXISTS bidder CASCADE;
DROP TABLE IF EXISTS auctionOwner CASCADE;
DROP TABLE IF EXISTS approvedAuction CASCADE;
DROP TABLE IF EXISTS removedAuction CASCADE;
DROP TABLE IF EXISTS bid CASCADE;

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
    type TEXT,
    PRIMARY KEY(id)
);


-- notification
CREATE TABLE notification(
    id SERIAL,
    date TIMESTAMP WITH TIME ZONE DEFAULT now(),
    information TEXT NOT NULL,
    viewed BOOLEAN NOT NULL DEFAULT FALSE,
    user_id INTEGER REFERENCES users,
    PRIMARY KEY(id)
);

-- auctionOwner
CREATE TABLE auctionOwner(
    user_id INTEGER REFERENCES users,
    PRIMARY KEY(user_id)
);

-- auction
CREATE TABLE auction(
    id SERIAL,
    title TEXT NOT NULL,
    description TEXT DEFAULT 'No Description',
    dateCreated TIMESTAMP WITH TIME ZONE NOT NULL,
    duration INTEGER NOT NULL,
    minValue NUMERIC NOT NULL,
    owner_id INTEGER REFERENCES auctionOwner(user_id),
    CONSTRAINT dateCreated_ck CHECK (dateCreated <= CURRENT_DATE),
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

-- adminstrator
CREATE TABLE administrator(
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    auction_id INTEGER REFERENCES auction,
    PRIMARY KEY(username)
);

-- bidder
CREATE TABLE bidder(
    user_id INTEGER REFERENCES users,
    PRIMARY KEY(user_id)
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
    auction_id INTEGER REFERENCES approvedAuction,
    bidder INTEGER REFERENCES bidder,
    bidDate TIMESTAMP WITH TIME ZONE NOT NULL,
    bidValue NUMERIC NOT NULL,
    winner BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY(auction_id, bidder)
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
