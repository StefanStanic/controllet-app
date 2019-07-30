INSERT INTO `category` (`id`, `category_name`) VALUES (NULL, 'Food & Drinks'), (NULL, 'Shopping'), (NULL, 'Housing'), (NULL, 'Transport'), (NULL, 'Vehicle'), (NULL, 'Life & Entertainment'), (NULL, 'Communications, PC'), (NULL, 'Financial expenses'), (NULL, 'Income');

INSERT INTO `sub_category` (`id`, `category_id`, `sub_category_name`) VALUES

-- Food and drinks
(NULL, '1', 'Bar, Cafe'),
(NULL, '1', 'Groceries'),
(NULL, '1', 'Restaurant, fast-food'),

-- Shopping
(NULL, '2', 'General - Shopping'),
(NULL, '2', 'Clothes & shoes'),
(NULL, '2', 'Drug-store, chemist'),
(NULL, '2', 'Electronics, accessories'),
(NULL, '2', 'Free time'),
(NULL, '2', 'Gifts, joy'),
(NULL, '2', 'Healt and beauty'),
(NULL, '2', 'Home, garden'),
(NULL, '2', 'Jewels, accessories'),
(NULL, '2', 'Kids'),
(NULL, '2', 'Pets, animals'),
(NULL, '2', 'Tools'),

-- Housing
(NULL, '3', 'General - Housing'),
(NULL, '3', 'Energy, utilities'),
(NULL, '3', 'Maintanance, repairs'),
(NULL, '3', 'Mortgage'),
(NULL, '3', 'Property insurance'),
(NULL, '3', 'Rent'),

-- Transport
(NULL, '4', 'General - Transportation'),
(NULL, '4', 'Business trips'),
(NULL, '4', 'Long distance'),
(NULL, '4', 'Public transport'),
(NULL, '4', 'Taxi'),

-- Vehicle
(NULL, '5', 'General - Vehicle'),
(NULL, '5', 'Fuel'),
(NULL, '5', 'Leasing'),
(NULL, '5', 'Parking'),
(NULL, '5', 'Rentals'),
(NULL, '5', 'Vehicle insurance'),

-- Life and Entertanment
(NULL, '6', 'Active sport, fitness'),
(NULL, '6', 'Alcohol, tobacco'),
(NULL, '6', 'Books, audio, subscriptions'),
(NULL, '6', 'Charity'),
(NULL, '6', 'Culture, sport events'),
(NULL, '6', 'Education, development'),
(NULL, '6', 'Hobbies'),
(NULL, '6', 'Holiday, trips, hotels'),
(NULL, '6', 'Life events'),
(NULL, '6', 'Lottery gambling'),


-- Communications, PC
(NULL, '7', 'Communications'),
(NULL, '7', 'Internet'),
(NULL, '7', 'Phone, mobile phone'),
(NULL, '7', 'Postal service'),
(NULL, '7', 'Software, apps, games'),

-- Financial expenses
(NULL, '8', 'Charges, Fees'),
(NULL, '8', 'Child support'),
(NULL, '8', 'Fines'),
(NULL, '8', 'Insurance'),
(NULL, '8', 'Loan'),
(NULL, '8', 'Taxes'),

-- Income
(NULL, '9', 'Checks, coupons'),
(NULL, '9', 'Dues & grants'),
(NULL, '9', 'Renting'),
(NULL, '9', 'Refunds'),
(NULL, '9', 'Wage, invoice');

INSERT INTO `transaction_type` (`id`, `transaction_type`) VALUES (NULL, 'Expense'), (NULL, 'Income');