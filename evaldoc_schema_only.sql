--
-- PostgreSQL database dump
--

\restrict 0qIGAHcPTGkDxKRBNIqu1spHK36k0eSQHEVwo5zcnzioQ1E9KJRQH679VaKU81h

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-01-06 23:59:44

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 229 (class 1259 OID 16547)
-- Name: departamentos; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.departamentos (
    id integer NOT NULL,
    nombre text NOT NULL,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.departamentos OWNER TO evaldoc;

--
-- TOC entry 228 (class 1259 OID 16546)
-- Name: departamentos_id_seq; Type: SEQUENCE; Schema: public; Owner: evaldoc
--

CREATE SEQUENCE public.departamentos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.departamentos_id_seq OWNER TO evaldoc;

--
-- TOC entry 5166 (class 0 OID 0)
-- Dependencies: 228
-- Name: departamentos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: evaldoc
--

ALTER SEQUENCE public.departamentos_id_seq OWNED BY public.departamentos.id;


--
-- TOC entry 225 (class 1259 OID 16518)
-- Name: docentes; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.docentes (
    usuario_id integer NOT NULL
);


ALTER TABLE public.docentes OWNER TO evaldoc;

--
-- TOC entry 224 (class 1259 OID 16501)
-- Name: estudiantes; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.estudiantes (
    usuario_id integer NOT NULL,
    matricula text NOT NULL
);


ALTER TABLE public.estudiantes OWNER TO evaldoc;

--
-- TOC entry 235 (class 1259 OID 16602)
-- Name: grupos_oferta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.grupos_oferta (
    id integer NOT NULL,
    periodo_id integer NOT NULL,
    materia_id integer NOT NULL,
    grupo text NOT NULL,
    docente_usuario_id integer NOT NULL,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.grupos_oferta OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 16601)
-- Name: grupos_oferta_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.grupos_oferta_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.grupos_oferta_id_seq OWNER TO postgres;

--
-- TOC entry 5167 (class 0 OID 0)
-- Dependencies: 234
-- Name: grupos_oferta_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.grupos_oferta_id_seq OWNED BY public.grupos_oferta.id;


--
-- TOC entry 239 (class 1259 OID 16657)
-- Name: import_lotes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.import_lotes (
    id integer NOT NULL,
    periodo_id integer NOT NULL,
    archivo_nombre text NOT NULL,
    archivo_hash text,
    estado text DEFAULT 'pendiente'::text NOT NULL,
    errores_count integer DEFAULT 0 NOT NULL,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.import_lotes OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 16656)
-- Name: import_lotes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.import_lotes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.import_lotes_id_seq OWNER TO postgres;

--
-- TOC entry 5168 (class 0 OID 0)
-- Dependencies: 238
-- Name: import_lotes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.import_lotes_id_seq OWNED BY public.import_lotes.id;


--
-- TOC entry 241 (class 1259 OID 16680)
-- Name: import_registros; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.import_registros (
    id integer NOT NULL,
    lote_id integer NOT NULL,
    row_num integer NOT NULL,
    departamento text,
    materia_nombre text,
    materia_clave text,
    grupo text,
    profesor_nombre text,
    profesor_email text,
    matricula text,
    alumno_nombre text,
    alumno_email text,
    procesado boolean DEFAULT false NOT NULL,
    error text,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.import_registros OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 16679)
-- Name: import_registros_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.import_registros_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.import_registros_id_seq OWNER TO postgres;

--
-- TOC entry 5169 (class 0 OID 0)
-- Dependencies: 240
-- Name: import_registros_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.import_registros_id_seq OWNED BY public.import_registros.id;


--
-- TOC entry 237 (class 1259 OID 16634)
-- Name: inscripciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inscripciones (
    id integer NOT NULL,
    grupo_oferta_id integer NOT NULL,
    estudiante_usuario_id integer NOT NULL,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.inscripciones OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 16633)
-- Name: inscripciones_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inscripciones_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.inscripciones_id_seq OWNER TO postgres;

--
-- TOC entry 5170 (class 0 OID 0)
-- Dependencies: 236
-- Name: inscripciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inscripciones_id_seq OWNED BY public.inscripciones.id;


--
-- TOC entry 231 (class 1259 OID 16562)
-- Name: jefaturas; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.jefaturas (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    departamento_id integer NOT NULL,
    periodo_id integer,
    activo boolean DEFAULT true NOT NULL,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.jefaturas OWNER TO evaldoc;

--
-- TOC entry 230 (class 1259 OID 16561)
-- Name: jefaturas_id_seq; Type: SEQUENCE; Schema: public; Owner: evaldoc
--

CREATE SEQUENCE public.jefaturas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jefaturas_id_seq OWNER TO evaldoc;

--
-- TOC entry 5171 (class 0 OID 0)
-- Dependencies: 230
-- Name: jefaturas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: evaldoc
--

ALTER SEQUENCE public.jefaturas_id_seq OWNED BY public.jefaturas.id;


--
-- TOC entry 233 (class 1259 OID 16581)
-- Name: materias; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.materias (
    id integer NOT NULL,
    clave text NOT NULL,
    nombre text NOT NULL,
    departamento_id integer,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.materias OWNER TO evaldoc;

--
-- TOC entry 232 (class 1259 OID 16580)
-- Name: materias_id_seq; Type: SEQUENCE; Schema: public; Owner: evaldoc
--

CREATE SEQUENCE public.materias_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.materias_id_seq OWNER TO evaldoc;

--
-- TOC entry 5172 (class 0 OID 0)
-- Dependencies: 232
-- Name: materias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: evaldoc
--

ALTER SEQUENCE public.materias_id_seq OWNED BY public.materias.id;


--
-- TOC entry 223 (class 1259 OID 16487)
-- Name: perfiles_usuario; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.perfiles_usuario (
    usuario_id integer NOT NULL,
    nombre_completo text NOT NULL,
    nombres text,
    apellidos text
);


ALTER TABLE public.perfiles_usuario OWNER TO evaldoc;

--
-- TOC entry 227 (class 1259 OID 16530)
-- Name: periodos; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.periodos (
    id integer NOT NULL,
    codigo text NOT NULL,
    fecha_inicio date,
    fecha_fin date,
    activo boolean DEFAULT true NOT NULL,
    creado_en timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.periodos OWNER TO evaldoc;

--
-- TOC entry 226 (class 1259 OID 16529)
-- Name: periodos_id_seq; Type: SEQUENCE; Schema: public; Owner: evaldoc
--

CREATE SEQUENCE public.periodos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.periodos_id_seq OWNER TO evaldoc;

--
-- TOC entry 5173 (class 0 OID 0)
-- Dependencies: 226
-- Name: periodos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: evaldoc
--

ALTER SEQUENCE public.periodos_id_seq OWNED BY public.periodos.id;


--
-- TOC entry 219 (class 1259 OID 16390)
-- Name: roles; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    rol text NOT NULL
);


ALTER TABLE public.roles OWNER TO evaldoc;

--
-- TOC entry 221 (class 1259 OID 16447)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: evaldoc
--

ALTER TABLE public.roles ALTER COLUMN id ADD GENERATED BY DEFAULT AS IDENTITY (
    SEQUENCE NAME public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 220 (class 1259 OID 16399)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: evaldoc
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nombre text NOT NULL,
    apaterno text NOT NULL,
    amaterno text,
    correo text NOT NULL,
    pass text NOT NULL,
    rol integer NOT NULL,
    activo boolean DEFAULT true
);


ALTER TABLE public.usuarios OWNER TO evaldoc;

--
-- TOC entry 222 (class 1259 OID 16457)
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: evaldoc
--

ALTER TABLE public.usuarios ALTER COLUMN id ADD GENERATED BY DEFAULT AS IDENTITY (
    SEQUENCE NAME public.usuarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 4917 (class 2604 OID 16550)
-- Name: departamentos id; Type: DEFAULT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.departamentos ALTER COLUMN id SET DEFAULT nextval('public.departamentos_id_seq'::regclass);


--
-- TOC entry 4924 (class 2604 OID 16605)
-- Name: grupos_oferta id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.grupos_oferta ALTER COLUMN id SET DEFAULT nextval('public.grupos_oferta_id_seq'::regclass);


--
-- TOC entry 4928 (class 2604 OID 16660)
-- Name: import_lotes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.import_lotes ALTER COLUMN id SET DEFAULT nextval('public.import_lotes_id_seq'::regclass);


--
-- TOC entry 4932 (class 2604 OID 16683)
-- Name: import_registros id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.import_registros ALTER COLUMN id SET DEFAULT nextval('public.import_registros_id_seq'::regclass);


--
-- TOC entry 4926 (class 2604 OID 16637)
-- Name: inscripciones id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones ALTER COLUMN id SET DEFAULT nextval('public.inscripciones_id_seq'::regclass);


--
-- TOC entry 4919 (class 2604 OID 16565)
-- Name: jefaturas id; Type: DEFAULT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.jefaturas ALTER COLUMN id SET DEFAULT nextval('public.jefaturas_id_seq'::regclass);


--
-- TOC entry 4922 (class 2604 OID 16584)
-- Name: materias id; Type: DEFAULT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.materias ALTER COLUMN id SET DEFAULT nextval('public.materias_id_seq'::regclass);


--
-- TOC entry 4914 (class 2604 OID 16533)
-- Name: periodos id; Type: DEFAULT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.periodos ALTER COLUMN id SET DEFAULT nextval('public.periodos_id_seq'::regclass);


--
-- TOC entry 5148 (class 0 OID 16547)
-- Dependencies: 229
-- Data for Name: departamentos; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5144 (class 0 OID 16518)
-- Dependencies: 225
-- Data for Name: docentes; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5143 (class 0 OID 16501)
-- Dependencies: 224
-- Data for Name: estudiantes; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5154 (class 0 OID 16602)
-- Dependencies: 235
-- Data for Name: grupos_oferta; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5158 (class 0 OID 16657)
-- Dependencies: 239
-- Data for Name: import_lotes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5160 (class 0 OID 16680)
-- Dependencies: 241
-- Data for Name: import_registros; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5156 (class 0 OID 16634)
-- Dependencies: 237
-- Data for Name: inscripciones; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5150 (class 0 OID 16562)
-- Dependencies: 231
-- Data for Name: jefaturas; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5152 (class 0 OID 16581)
-- Dependencies: 233
-- Data for Name: materias; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5142 (class 0 OID 16487)
-- Dependencies: 223
-- Data for Name: perfiles_usuario; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5146 (class 0 OID 16530)
-- Dependencies: 227
-- Data for Name: periodos; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5138 (class 0 OID 16390)
-- Dependencies: 219
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5139 (class 0 OID 16399)
-- Dependencies: 220
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5174 (class 0 OID 0)
-- Dependencies: 228
-- Name: departamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5175 (class 0 OID 0)
-- Dependencies: 234
-- Name: grupos_oferta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--



--
-- TOC entry 5176 (class 0 OID 0)
-- Dependencies: 238
-- Name: import_lotes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--



--
-- TOC entry 5177 (class 0 OID 0)
-- Dependencies: 240
-- Name: import_registros_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--



--
-- TOC entry 5178 (class 0 OID 0)
-- Dependencies: 236
-- Name: inscripciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--



--
-- TOC entry 5179 (class 0 OID 0)
-- Dependencies: 230
-- Name: jefaturas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5180 (class 0 OID 0)
-- Dependencies: 232
-- Name: materias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 226
-- Name: periodos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 221
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--



--
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 222
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: evaldoc
--



--
-- TOC entry 4955 (class 2606 OID 16560)
-- Name: departamentos departamentos_nombre_key; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.departamentos
    ADD CONSTRAINT departamentos_nombre_key UNIQUE (nombre);


--
-- TOC entry 4957 (class 2606 OID 16558)
-- Name: departamentos departamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.departamentos
    ADD CONSTRAINT departamentos_pkey PRIMARY KEY (id);


--
-- TOC entry 4949 (class 2606 OID 16523)
-- Name: docentes docentes_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.docentes
    ADD CONSTRAINT docentes_pkey PRIMARY KEY (usuario_id);


--
-- TOC entry 4945 (class 2606 OID 16511)
-- Name: estudiantes estudiantes_matricula_key; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_matricula_key UNIQUE (matricula);


--
-- TOC entry 4947 (class 2606 OID 16509)
-- Name: estudiantes estudiantes_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_pkey PRIMARY KEY (usuario_id);


--
-- TOC entry 4965 (class 2606 OID 16616)
-- Name: grupos_oferta grupos_oferta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.grupos_oferta
    ADD CONSTRAINT grupos_oferta_pkey PRIMARY KEY (id);


--
-- TOC entry 4971 (class 2606 OID 16673)
-- Name: import_lotes import_lotes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.import_lotes
    ADD CONSTRAINT import_lotes_pkey PRIMARY KEY (id);


--
-- TOC entry 4973 (class 2606 OID 16694)
-- Name: import_registros import_registros_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.import_registros
    ADD CONSTRAINT import_registros_pkey PRIMARY KEY (id);


--
-- TOC entry 4968 (class 2606 OID 16644)
-- Name: inscripciones inscripciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones
    ADD CONSTRAINT inscripciones_pkey PRIMARY KEY (id);


--
-- TOC entry 4959 (class 2606 OID 16574)
-- Name: jefaturas jefaturas_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.jefaturas
    ADD CONSTRAINT jefaturas_pkey PRIMARY KEY (id);


--
-- TOC entry 4961 (class 2606 OID 16595)
-- Name: materias materias_clave_key; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.materias
    ADD CONSTRAINT materias_clave_key UNIQUE (clave);


--
-- TOC entry 4963 (class 2606 OID 16593)
-- Name: materias materias_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.materias
    ADD CONSTRAINT materias_pkey PRIMARY KEY (id);


--
-- TOC entry 4943 (class 2606 OID 16495)
-- Name: perfiles_usuario perfiles_usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.perfiles_usuario
    ADD CONSTRAINT perfiles_usuario_pkey PRIMARY KEY (usuario_id);


--
-- TOC entry 4951 (class 2606 OID 16545)
-- Name: periodos periodos_codigo_key; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.periodos
    ADD CONSTRAINT periodos_codigo_key UNIQUE (codigo);


--
-- TOC entry 4953 (class 2606 OID 16543)
-- Name: periodos periodos_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.periodos
    ADD CONSTRAINT periodos_pkey PRIMARY KEY (id);


--
-- TOC entry 4936 (class 2606 OID 16434)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 4939 (class 2606 OID 16459)
-- Name: usuarios usuarios_correo_unique; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_correo_unique UNIQUE (correo);


--
-- TOC entry 4941 (class 2606 OID 16449)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 4974 (class 1259 OID 16700)
-- Name: ix_import_registros_lote; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX ix_import_registros_lote ON public.import_registros USING btree (lote_id);


--
-- TOC entry 4975 (class 1259 OID 16701)
-- Name: ix_import_registros_procesado; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX ix_import_registros_procesado ON public.import_registros USING btree (lote_id, procesado);


--
-- TOC entry 4937 (class 1259 OID 16712)
-- Name: ix_usuarios_activo; Type: INDEX; Schema: public; Owner: evaldoc
--

CREATE INDEX ix_usuarios_activo ON public.usuarios USING btree (activo);


--
-- TOC entry 4966 (class 1259 OID 16632)
-- Name: ux_grupos_oferta; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX ux_grupos_oferta ON public.grupos_oferta USING btree (periodo_id, materia_id, grupo, docente_usuario_id);


--
-- TOC entry 4969 (class 1259 OID 16655)
-- Name: ux_inscripciones; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX ux_inscripciones ON public.inscripciones USING btree (grupo_oferta_id, estudiante_usuario_id);


--
-- TOC entry 4979 (class 2606 OID 16524)
-- Name: docentes fk_docente_usuario; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.docentes
    ADD CONSTRAINT fk_docente_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4978 (class 2606 OID 16512)
-- Name: estudiantes fk_estudiante_usuario; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT fk_estudiante_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4989 (class 2606 OID 16674)
-- Name: import_lotes fk_import_lote_periodo; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.import_lotes
    ADD CONSTRAINT fk_import_lote_periodo FOREIGN KEY (periodo_id) REFERENCES public.periodos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4990 (class 2606 OID 16695)
-- Name: import_registros fk_import_registro_lote; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.import_registros
    ADD CONSTRAINT fk_import_registro_lote FOREIGN KEY (lote_id) REFERENCES public.import_lotes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4987 (class 2606 OID 16650)
-- Name: inscripciones fk_inscripcion_estudiante_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones
    ADD CONSTRAINT fk_inscripcion_estudiante_usuario FOREIGN KEY (estudiante_usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4988 (class 2606 OID 16645)
-- Name: inscripciones fk_inscripcion_oferta; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inscripciones
    ADD CONSTRAINT fk_inscripcion_oferta FOREIGN KEY (grupo_oferta_id) REFERENCES public.grupos_oferta(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4980 (class 2606 OID 16702)
-- Name: jefaturas fk_jefatura_departamento; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.jefaturas
    ADD CONSTRAINT fk_jefatura_departamento FOREIGN KEY (departamento_id) REFERENCES public.departamentos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4981 (class 2606 OID 16707)
-- Name: jefaturas fk_jefatura_periodo; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.jefaturas
    ADD CONSTRAINT fk_jefatura_periodo FOREIGN KEY (periodo_id) REFERENCES public.periodos(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4982 (class 2606 OID 16575)
-- Name: jefaturas fk_jefatura_usuario; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.jefaturas
    ADD CONSTRAINT fk_jefatura_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4983 (class 2606 OID 16596)
-- Name: materias fk_materia_departamento; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.materias
    ADD CONSTRAINT fk_materia_departamento FOREIGN KEY (departamento_id) REFERENCES public.departamentos(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4984 (class 2606 OID 16627)
-- Name: grupos_oferta fk_oferta_docente_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.grupos_oferta
    ADD CONSTRAINT fk_oferta_docente_usuario FOREIGN KEY (docente_usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4985 (class 2606 OID 16622)
-- Name: grupos_oferta fk_oferta_materia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.grupos_oferta
    ADD CONSTRAINT fk_oferta_materia FOREIGN KEY (materia_id) REFERENCES public.materias(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4986 (class 2606 OID 16617)
-- Name: grupos_oferta fk_oferta_periodo; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.grupos_oferta
    ADD CONSTRAINT fk_oferta_periodo FOREIGN KEY (periodo_id) REFERENCES public.periodos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4977 (class 2606 OID 16496)
-- Name: perfiles_usuario fk_perfil_usuario; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.perfiles_usuario
    ADD CONSTRAINT fk_perfil_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4976 (class 2606 OID 16436)
-- Name: usuarios fk_rol; Type: FK CONSTRAINT; Schema: public; Owner: evaldoc
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT fk_rol FOREIGN KEY (rol) REFERENCES public.roles(id);


-- Completed on 2026-01-06 23:59:44

--
-- PostgreSQL database dump complete
--

\unrestrict 0qIGAHcPTGkDxKRBNIqu1spHK36k0eSQHEVwo5zcnzioQ1E9KJRQH679VaKU81h

