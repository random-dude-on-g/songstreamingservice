-- Insert data into ACCOUNT table
INSERT INTO ACCOUNT (ACC_NAME, ACC_PASSWORD) VALUES 
('Alice', 'pass123!'),
('Bob', 'qwerty456'),
('Charlie', 'zxcvbn789'),
('Admin', 'abcd@1234'),
('Admin', '5678efgh'),
('Frank', '9ij!klmn'),
('Grace', 'opqr!567'),
('Heidi', 'stuv@890'),
('Admin', 'wxyz!123'),
('Judy', 'lmno@456');

-- Insert data into CUSTOMER table
INSERT INTO CUSTOMER (ACC_ID, SUBSCRIPTION_STATUS) VALUES 
(1, 1),
(2, 1),
(3, 0),
(6, 1),
(7, 1),
(8, 0),
(10, 0);

-- Insert data into STAFF table
INSERT INTO STAFF (ACC_ID, STAFF_NAME) VALUES 
(4, 'Dave'),
(5, 'Eve'),
(9, 'Ivan');

-- Insert data into SUBSCRIPTION table
INSERT INTO SUBSCRIPTION (ACC_ID) VALUES 
(1),
(2),
(6),
(7);

-- Insert data into LANGUAGE table
INSERT INTO LANGUAGE (LANG_NAME) VALUES 
('English'),
('Japanese'),
('Korean'),
('Indonesian');

-- Insert data into SINGER table
INSERT INTO SINGER (SINGER_NAME) VALUES 
('Why Don\'t We'),
('Ado'),
('XG'),
('Yasuha'),
('Yovie');

-- Insert data into SONG table
INSERT INTO SONG (SONG_TITLE, SONG_RELEASE_YEAR, SONG_DURATION, SINGER_ID, LANG_ID) VALUES 
('These Girls', 2019, '00:02:53', 1, 1),
('Gira-Gira', 2021, '00:04:43', 2, 2),
('Woke Up', 2024, '00:03:13', 3, 3),
('Flyday Chinatown', 1981, '00:03:34', 4, 2),
('Menjaga Hati', 2007, '00:03:58', 5, 4);

-- Insert data into PLAYLIST table
INSERT INTO PLAYLIST (ACC_ID, SUB_ID, PLAYLIST_NAME) VALUES 
(1, 1, 'Playlist 1'),
(6, 3, 'Playlist 2'),
(7, 4, 'Playlist 3'),
(2, 2, 'Playlist 4');

-- Insert data into MANAGE table
INSERT INTO MANAGE (SONG_ID, ACC_ID) VALUES 
(1, 4),
(2, 4),
(3, 5),
(4, 9),
(5, 5);

-- Insert data into STORE table
INSERT INTO STORE (PLAYLIST_ID, SONG_ID) VALUES 
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(3, 5),
(4, 1),
(4, 4);

-- Insert data into HISTORY table
INSERT INTO HISTORY (SONG_ID, ACC_ID, DURATION_LISTENED) VALUES 
(1, 1, '00:01:30'),
(2, 2, '00:02:00'),
(3, 3, '00:00:45'),
(4, 3, '00:01:10'),
(5, 6, '00:02:15');
