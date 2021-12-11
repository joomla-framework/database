-- Purposefully do not add the table prefix to this file as it is mainly processed by the psql CLI or an application like pgAdmin
CREATE TABLE "dbtest" (
  "id" serial NOT NULL,
  "title" character varying(50) NOT NULL,
  "start_date" timestamp without time zone NOT NULL,
  "description" text NOT NULL,
  "data" bytea,
  PRIMARY KEY ("id")
);
