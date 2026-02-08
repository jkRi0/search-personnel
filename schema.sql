-- Run this in phpMyAdmin or MySQL client

CREATE DATABASE IF NOT EXISTS personnel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE personnel_db;

CREATE TABLE offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    office_name VARCHAR(150) NOT NULL
);

CREATE TABLE personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    designation VARCHAR(200) NOT NULL,
    office_id INT NOT NULL,
    FOREIGN KEY (office_id) REFERENCES offices(id)
);

-- Sample data
INSERT INTO offices (office_name) VALUES
('Schools Division Superintendent'),
('Assistant Schools Division Superintendent'),
('Administrative Services'),
('Personnel'),
('Records'),
('Procurement'),
('Property and Supply'),
('Cashier'),
('Finance Services - Accounting'),
('Finance Services - Budget'),
('Legal Services'),
('ICT Services'),
('Curriculum Implementation Division'),
('Learning Resource Management'),
('Instructional Management'),
('District Instructional Supervision'),
('Alternative Learning System'),
('School Governance and Operations Division'),
('Educational Facilities'),
('Disaster Risk Reduction Management'),
('School Management Monitoring and Evaluation'),
('Social Mobilization and Networking'),
('Human Resource Development'),
('Planning and Research'),
('School Health Section');

INSERT INTO personnel (full_name, designation, office_id) VALUES
('Christopher R. Diaz, CESO V', 'Schools Division Superintendent', 1),
('Aubrey Anne A. Tablan', 'Administrative Assistant III', 1),
('Ann Lorraine A. Chavez', 'Administrative Assistant I', 1),
('Wendy M. Avila', 'LSB – Utility Worker', 1),
('Antonio A. Albay', 'LSB – Driver', 1),

('Randy D. Punzalan, CESO VI', 'Assistant Schools Division Superintendent', 2),
('Axel Von E. Baron', 'Administrative Aide VI', 2),
('Jeffree Gil C. Alinsunurin', 'Administrative Aide IV', 2),

('Noel G. Sequito, EdD', 'Administrative Officer V', 3),
('Chrizl Lee B. Ledesma', 'Administrative Aide VI', 3),
('Sheryl V. Punongbayan', 'LSB – Job Order', 3),
('Joart C. Tampelic', 'Utility Worker I', 3),
('Herminio V. Idian', 'Utility Worker I', 3),
('Melanie L. Completo', 'JO–MOOE (Administrative Aide I)', 3),
('Carmelito D. Hercia', 'JO–MOOE (Driver)', 3),
('Pepito B. Hemedez', 'Administrative Aide III', 3),

('Jhoanna M. Manzanero', 'Administrative Officer IV', 4),
('Marilyn M. Ramos', 'Administrative Officer II', 4),
('Mary Anne N. Dinulos', 'Administrative Assistant III', 4),
('Jhockey Lyn M. Bariring', 'Administrative Assistant III', 4),
('Ray Mark D. Teodoro', 'Administrative Assistant III', 4),
('Ana Ruby B. Mamplata', 'LSB Clerk', 4),
('Sheryl A. Bariring', 'Administrative Assistant III', 4),
('Jesse Coleen J. Sayson', 'Administrative Aide VI', 4),
('Ellaica Jane D. Petate', 'Administrative Assistant III', 4),
('Rigor Brando C. Estanislao', 'Administrative Assistant III', 4),
('Glizelda T. Flores', 'Administrative Assistant III', 4),

('Aileen M. Bartido', 'Administrative Officer IV', 5),
('Rina Jamile O. Gimutao', 'Administrative Aide VI', 5),
('Maria Rowena B. Matta', 'LSB Clerk', 5),
('Jonalyn B. Tuiza', 'LSB Job Order', 5),

('Kamille Rose S. Mendoza', 'Administrative Officer IV', 6),
('Kent Gabriel E. Caparas', 'Administrative Officer II', 6),

('Rio Jean M. Muzares', 'Administrative Officer IV', 7),
('Sharon D. Gotiongco', 'Administrative Assistant III', 7),
('Paul Ryan Encina', 'Administrative Aide VI', 7),

('Anneslee A. Riñon', 'Administrative Officer IV', 8),
('Ben Carlos A. Bella', 'Administrative Assistant II', 8),
('Clark Bella', 'Administrative Aide VI', 8),

('Nathalie Joy U. Don', 'Accountant III', 9),
('Zandy Charlene J. Sanchez', 'Administrative Assistant III', 9),
('Maria Toni G. Galicia', 'Administrative Assistant III', 9),
('Sheryl Ann Teodoro', 'Administrative Assistant III', 9),
('Dino Larino', 'Administrative Assistant II', 9),
('Alerry M. Layog', 'Administrative Assistant III - LGU', 9),
('Geramie S. Villapando', 'Administrative Assistant II', 9),

('Nida E. Elago', 'Administrative Officer V', 10),
('Rose May G. Dagsil', 'Administrative Assistant III', 10),
('Rainchelle Marie B. Tuscano', 'Administrative Aide VI', 10),

('Jerica Clara S. Machado-Dela Peña', 'Attorney III', 11),
('Racel Amor E. Escolano', 'Legal Assistant I', 11),

('Chem Jayder M. Cabungcal', 'Information Technology Officer I', 12),

('Edna F. Hemedez, EdD', 'Chief, Curriculum Implementation Division', 13),
('Malcolm Ray I. Franco', 'Administrative Aide VI', 13),
('Paul Ryan E. Ceperes', 'LSB Clerk', 13),

('Jackie Lou A. Almira, PhD', 'Education Program Supervisor - LRMS', 14),
('Vacant', 'Project Development Officer II', 14),
('Allaine Jean N. Guerta', 'Librarian II', 14),

('Jonathan F. Bernabe, EdD', 'Education Program Supervisor - Filipino', 15),
('Jonathan H. Marquez, PhD', 'Education Program Supervisor - English', 15),
('Alberto A. Labigan, EdD', 'Education Program Supervisor - Mathematics', 15),
('Ma. Leonora N. Natividad', 'Education Program Supervisor - Science', 15),
('Maribeth G. Herrerro', 'Education Program Supervisor - Araling Panlipunan', 15),
('Philip D. Cruz', 'Education Program Supervisor - EsP/SNeD', 15),
('Grace C. Endaya, EdD', 'Education Program Supervisor - EPP/TLE/TVL', 15),
('Buena G. Villanueva', 'Education Program Supervisor - Kindergarten', 15),
('Marianne A. Velasco, EdD', 'Education Program Supervisor - MAPEH', 15),
('Leonilo Dee, JR', 'Technical Assistant I - Sports Coordinator', 15),

('Irene P. Pantonial, PhD', 'PSDS - District 1', 16),
('Allan D. Cantalejo', 'PSDS - District 1', 16),
('Nimcy M. Ortiz, EdD', 'PSDS - District 2', 16),
('Jennettee F. Haro, EdD', 'PSDS - District 2', 16),
('Jean E. Paz', 'PSDS - District 3', 16),
('Racquel C. Austria, EdD', 'PSDS - District 3', 16),
('Belen G. Gimuta, EdD', 'PSDS - District 4', 16),
('Ma. Theresa S. Ramos, EdD', 'PSDS - District 4', 16),
('Lourdes A. Terrones', 'PSDS - District 5', 16),
('Reynald O. Talavera, EdD', 'PSDS - District 5', 16),

('Allen Cris Montillano, PhD', 'Education Program Specialist II', 17),
('Juno S. Gavasan', 'Education Program Specialist II', 17),

('Jose Charlie S. Aloquin, PhD', 'OIC - Chief, School Governance and Operation Division', 18),
('Reyarr L. Cruz', 'OIC - Education Program Supervisor', 18),
('Annabelle B. Morales', 'Administrative Assistant III', 18),
('Regina D. Cantillan', 'Administrative Aide III', 18),
('Cristina Cheryl I. Bautista', 'Administrative Officer III', 18),

('Sarah B. Castillo-Lagrada', 'Engineer III', 19),

('Jomar D. Flores', 'Project Development Officer II', 20),
('Erica Shaina T. Castor', 'Administrative Support II / DRRM', 20),

('Marvin R. Vicente, PhD', 'Senior Education Program Specialist', 21),
('Vacant', 'Education Program Specialist II', 21),

('Ronnie Z. Villanueva, PhD', 'Senior Education Program Specialist', 22),
('Josiel Joulie L. Punongbayan', 'Education Program Specialist II', 22),

('Tomas D. Dorado, EdD', 'Senior Education Program Specialist', 23),
('Romel A. Delingon', 'Education Program Specialist II', 23),

('Jeffrey A. Astillero, PhD', 'Senior Education Program Specialist', 24),
('Troy Allan H. Pedron', 'Planning Officer III', 24),

('Donna Jean B. Añon, MD', 'Medical Officer III', 25),
('Joy O. Andaya', 'Nurse II', 25),
('Gilbert C. Bagsic', 'Nurse II', 25),
('Mario V. Ramilo, Jr.', 'Nurse II', 25),
('Jhunel Q. Saguni', 'Nurse II', 25),
('Edmon O. Galang', 'Nurse II', 25),
('Danielle Anne C. Cecilia', 'Nurse I', 25),
('Melanie M. Langue', 'Nurse I', 25),
('Ruel U. Capistrano, DMD', 'Dentist II', 25),
('Angela Marie E. Mapola, DMD', 'Dentist II', 25),
('Demetrio G. Gamo', 'Technical Assistant I / WinS Program, GPP', 25),
('Ralph Lauren O. Andaya', 'Technical Assistant I / SBFP', 25);
