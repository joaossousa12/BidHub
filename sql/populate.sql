SET search_path TO lbaw2364;

INSERT INTO users (username, email, password, address, postalCode, phoneNumber, type)
VALUES
    ('António', 'antonio13@gmail.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Rua das Flores', '4000-123', '+351968374465', 'auctionowner'),
    ('José', 'jose2002@gmail.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Rua dos castelos', '4403-122', '+351968374463', 'auctionowner'),
    ('Marcelo', 'marcelorebelo@gmail.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Avenida José Matos', '4222-112', '+3519683744651', 'bidder'),
    ('Ana', 'ana2@gmail.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Avenida da Boavista', '4374-132', '+351968374466', 'bidder');


INSERT INTO notification (information, user_id)
VALUES
    ('Your auction has ended!', 2),
    ('You got outbid!', 3),
    ('New auctions are available!', 4);

INSERT INTO auctionOwner (user_id)
VALUES
    (1),
    (2);

INSERT INTO auction (title, description, dateCreated, duration, minValue, owner_id)
VALUES
    ('Toyota Corolla', 'Good car from 2003 semi-new!', '2023-10-23', 7, 30000, 1),
    ('Gaming PC Asus', '2023 pc with all the games!', '2023-10-22', 5, 1000, 2);

INSERT INTO category (categoryName)
VALUES
    ('Cars'),
    ('PC'),
    ('Phones');

INSERT INTO belongs (auction_id, categoryName)
VALUES
    (1, 'Cars'),
    (2, 'PC');

INSERT INTO auctionModification (dateRequest, newDescription, approvedDate, auction_id)
VALUES
    ('2023-10-25', 'Good car from 2003 semi-new!(only comes with 3 wheels)', '2023-10-26', 1),
    ('2023-10-26', 'Asus pcwith all the games!', '2023-10-27', 2);

INSERT INTO administrator (username, password, auction_id)
VALUES
    ('admin1', 'e00cf25ad42683b3df678c61f42c6bda', 1);

INSERT INTO bidder (user_id)
VALUES
    (3),
    (4);

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
    (2, 4, '2023-10-30', 160, true);

