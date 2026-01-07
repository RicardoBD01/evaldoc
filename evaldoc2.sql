--
-- PostgreSQL database dump
--

\restrict c1vcekMVw1o9vtnJN5SvdGtvi5jLm4FJ7BDObl55mzhkg9MEMKdbgm8VwZZ7nBx

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-01-07 03:01:41

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 5151 (class 0 OID 16547)
-- Dependencies: 229
-- Data for Name: departamentos; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5147 (class 0 OID 16518)
-- Dependencies: 225
-- Data for Name: docentes; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5146 (class 0 OID 16501)
-- Dependencies: 224
-- Data for Name: estudiantes; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5157 (class 0 OID 16602)
-- Dependencies: 235
-- Data for Name: grupos_oferta; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5161 (class 0 OID 16657)
-- Dependencies: 239
-- Data for Name: import_lotes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5163 (class 0 OID 16680)
-- Dependencies: 241
-- Data for Name: import_registros; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5159 (class 0 OID 16634)
-- Dependencies: 237
-- Data for Name: inscripciones; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5153 (class 0 OID 16562)
-- Dependencies: 231
-- Data for Name: jefaturas; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5155 (class 0 OID 16581)
-- Dependencies: 233
-- Data for Name: materias; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5145 (class 0 OID 16487)
-- Dependencies: 223
-- Data for Name: perfiles_usuario; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5149 (class 0 OID 16530)
-- Dependencies: 227
-- Data for Name: periodos; Type: TABLE DATA; Schema: public; Owner: evaldoc
--

INSERT INTO public.periodos VALUES (1, '2025-1', NULL, NULL, true, '2026-01-06 17:15:28.947788');


--
-- TOC entry 5141 (class 0 OID 16390)
-- Dependencies: 219
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: evaldoc
--

INSERT INTO public.roles VALUES (1, 'Admin');
INSERT INTO public.roles VALUES (2, 'Docente');
INSERT INTO public.roles VALUES (3, 'Estudiante');
INSERT INTO public.roles VALUES (4, 'Jefe de departamento');


--
-- TOC entry 5142 (class 0 OID 16399)
-- Dependencies: 220
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: evaldoc
--

INSERT INTO public.usuarios VALUES (3, 'Lorelay', 'Martinez', 'Visairo', 'estudiante@cenidet.tecnm.mx', '$2y$12$AQS6wTxU8KEBa0Tqncr6Nuej7Bfd1FuxQM6zm1qy7.9uLPgSsQzBK', 3, true, false);
INSERT INTO public.usuarios VALUES (1, 'admin', 'padmin', 'madmin', 'admin@admin.com', '$2y$12$o.eZgmzB1.3EyBd9aqY9cOdUHOFQ6eE90oPoOSTx.2Vj4smKQzjsS', 1, true, false);
INSERT INTO public.usuarios VALUES (2, 'Ricardo', 'Barcenas', 'Dirzo', 'docentes@cenidet.tecnm.mx', '$2y$12$C7eCnz7EzeNusbniC5JdF.LXD79HOnCORCShk8NleEIB9Hcamd8g2', 2, true, false);
INSERT INTO public.usuarios VALUES (4, 'prueba', 'dadd', 'daada', 'fs@fas.com', '$2y$12$JWvGe7.OIqMo6QQ5M7SfCuyhU2mb5vQdKJ3.ncbcGkBqTIdkhTdei', 3, false, false);


--
-- TOC entry 5177 (class 0 OID 0)
-- Dependencies: 228
-- Name: departamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--

SELECT pg_catalog.setval('public.departamentos_id_seq', 1, false);


--
-- TOC entry 5178 (class 0 OID 0)
-- Dependencies: 234
-- Name: grupos_oferta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.grupos_oferta_id_seq', 1, false);


--
-- TOC entry 5179 (class 0 OID 0)
-- Dependencies: 238
-- Name: import_lotes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.import_lotes_id_seq', 1, false);


--
-- TOC entry 5180 (class 0 OID 0)
-- Dependencies: 240
-- Name: import_registros_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.import_registros_id_seq', 1, false);


--
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 236
-- Name: inscripciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inscripciones_id_seq', 1, false);


--
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 230
-- Name: jefaturas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--

SELECT pg_catalog.setval('public.jefaturas_id_seq', 1, false);


--
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 232
-- Name: materias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--

SELECT pg_catalog.setval('public.materias_id_seq', 1, false);


--
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 226
-- Name: periodos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--

SELECT pg_catalog.setval('public.periodos_id_seq', 1, true);


--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 221
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--

SELECT pg_catalog.setval('public.roles_id_seq', 4, true);


--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 222
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 4, true);


-- Completed on 2026-01-07 03:01:41

--
-- PostgreSQL database dump complete
--

\unrestrict c1vcekMVw1o9vtnJN5SvdGtvi5jLm4FJ7BDObl55mzhkg9MEMKdbgm8VwZZ7nBx

