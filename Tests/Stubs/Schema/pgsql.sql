CREATE TABLE "#__dbtest" (
  "id" serial NOT NULL,
  "title" character varying(50) NOT NULL,
  "start_date" timestamp without time zone NOT NULL,
  "description" text NOT NULL,
  "data" bytea,
  PRIMARY KEY ("id")
);
