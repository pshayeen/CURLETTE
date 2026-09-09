-- Run this if you already have a database set up with the old placeholder
-- prices. Matches by product name, only updates the price column.

USE curlette_db;

UPDATE product SET price = 380.00 WHERE name = 'Curl Cleansing Conditioner';
UPDATE product SET price = 380.00 WHERE name = 'Moisturising Conditioner';
UPDATE product SET price = 450.00 WHERE name = 'Curl Moisturising Treatment';
UPDATE product SET price = 450.00 WHERE name = 'Curl Protein Treatment';
UPDATE product SET price = 250.00 WHERE name = 'Detangling Wide-Tooth Comb';
UPDATE product SET price = 350.00 WHERE name = 'Leave-In Conditioner';
UPDATE product SET price = 420.00 WHERE name = 'Curl Defining Gel';
UPDATE product SET price = 650.00 WHERE name = 'Silk Pillowcase';
UPDATE product SET price = 300.00 WHERE name = 'Microfiber Curl Towel';
UPDATE product SET price = 320.00 WHERE name = 'Curl Refresher Spray';
