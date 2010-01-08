alter table users add column fte real;
alter table users add column classified_id integer;
alter table users add column manager_id integer;
alter table users add constraint fk38b73479ed2a3e7a FOREIGN KEY (manager_id) REFERENCES users(id);
