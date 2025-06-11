--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5 (Ubuntu 17.5-1.pgdg24.04+1)

-- Started on 2025-06-11 12:32:20 -03

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
-- TOC entry 855 (class 1247 OID 24586)
-- Name: genero_enum; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.genero_enum AS ENUM (
    'masculino',
    'feminino',
    'não-binario',
    'outro'
);


--
-- TOC entry 858 (class 1247 OID 24596)
-- Name: genero_interesse_enum; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.genero_interesse_enum AS ENUM (
    'masculino',
    'feminino',
    'todos'
);


--
-- TOC entry 861 (class 1247 OID 24604)
-- Name: orientacao_enum; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.orientacao_enum AS ENUM (
    'hetero',
    'gay',
    'lesbica',
    'bi',
    'pan',
    'outro'
);


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 220 (class 1259 OID 24643)
-- Name: curtidas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.curtidas (
    id integer NOT NULL,
    quem_curtiu integer,
    quem_foi_curtido integer,
    criado_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 219 (class 1259 OID 24642)
-- Name: curtidas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.curtidas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3400 (class 0 OID 0)
-- Dependencies: 219
-- Name: curtidas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.curtidas_id_seq OWNED BY public.curtidas.id;


--
-- TOC entry 222 (class 1259 OID 24661)
-- Name: matches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.matches (
    id integer NOT NULL,
    usuario1_id integer,
    usuario2_id integer,
    criado_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 221 (class 1259 OID 24660)
-- Name: matches_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.matches_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3401 (class 0 OID 0)
-- Dependencies: 221
-- Name: matches_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.matches_id_seq OWNED BY public.matches.id;


--
-- TOC entry 224 (class 1259 OID 24679)
-- Name: mensagens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.mensagens (
    id integer NOT NULL,
    match_id integer,
    remetente_id integer,
    texto text NOT NULL,
    enviada_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 223 (class 1259 OID 24678)
-- Name: mensagens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.mensagens_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3402 (class 0 OID 0)
-- Dependencies: 223
-- Name: mensagens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.mensagens_id_seq OWNED BY public.mensagens.id;


--
-- TOC entry 226 (class 1259 OID 32769)
-- Name: password_resets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_resets (
    id integer NOT NULL,
    user_id integer,
    token character varying(255) NOT NULL,
    expires_at timestamp without time zone NOT NULL,
    criado_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- TOC entry 225 (class 1259 OID 32768)
-- Name: password_resets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.password_resets_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3403 (class 0 OID 0)
-- Dependencies: 225
-- Name: password_resets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.password_resets_id_seq OWNED BY public.password_resets.id;


--
-- TOC entry 218 (class 1259 OID 24618)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    senha character varying(255) NOT NULL,
    genero public.genero_enum NOT NULL,
    bio text,
    nascimento date NOT NULL,
    criado_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    genero_interesse public.genero_interesse_enum,
    orientacao public.orientacao_enum,
    idade integer,
    gostos text[],
    online boolean DEFAULT false,
    ativo boolean DEFAULT false,
    token character varying(255),
    data_ultimo_login timestamp without time zone,
    foto_perfil text DEFAULT 'photos/default-profilepicture.jpg'::text
);


--
-- TOC entry 217 (class 1259 OID 24617)
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3404 (class 0 OID 0)
-- Dependencies: 217
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- TOC entry 3221 (class 2604 OID 24646)
-- Name: curtidas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.curtidas ALTER COLUMN id SET DEFAULT nextval('public.curtidas_id_seq'::regclass);


--
-- TOC entry 3223 (class 2604 OID 24664)
-- Name: matches id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.matches ALTER COLUMN id SET DEFAULT nextval('public.matches_id_seq'::regclass);


--
-- TOC entry 3225 (class 2604 OID 24682)
-- Name: mensagens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mensagens ALTER COLUMN id SET DEFAULT nextval('public.mensagens_id_seq'::regclass);


--
-- TOC entry 3227 (class 2604 OID 32772)
-- Name: password_resets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_resets ALTER COLUMN id SET DEFAULT nextval('public.password_resets_id_seq'::regclass);


--
-- TOC entry 3216 (class 2604 OID 24621)
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- TOC entry 3234 (class 2606 OID 24649)
-- Name: curtidas curtidas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.curtidas
    ADD CONSTRAINT curtidas_pkey PRIMARY KEY (id);


--
-- TOC entry 3236 (class 2606 OID 24667)
-- Name: matches matches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.matches
    ADD CONSTRAINT matches_pkey PRIMARY KEY (id);


--
-- TOC entry 3238 (class 2606 OID 24687)
-- Name: mensagens mensagens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mensagens
    ADD CONSTRAINT mensagens_pkey PRIMARY KEY (id);


--
-- TOC entry 3240 (class 2606 OID 32775)
-- Name: password_resets password_resets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_pkey PRIMARY KEY (id);


--
-- TOC entry 3242 (class 2606 OID 32777)
-- Name: password_resets password_resets_token_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_token_key UNIQUE (token);


--
-- TOC entry 3230 (class 2606 OID 24628)
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- TOC entry 3232 (class 2606 OID 24626)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 3243 (class 2606 OID 24650)
-- Name: curtidas curtidas_quem_curtiu_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.curtidas
    ADD CONSTRAINT curtidas_quem_curtiu_fkey FOREIGN KEY (quem_curtiu) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3244 (class 2606 OID 24655)
-- Name: curtidas curtidas_quem_foi_curtido_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.curtidas
    ADD CONSTRAINT curtidas_quem_foi_curtido_fkey FOREIGN KEY (quem_foi_curtido) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3245 (class 2606 OID 24668)
-- Name: matches matches_usuario1_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.matches
    ADD CONSTRAINT matches_usuario1_id_fkey FOREIGN KEY (usuario1_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3246 (class 2606 OID 24673)
-- Name: matches matches_usuario2_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.matches
    ADD CONSTRAINT matches_usuario2_id_fkey FOREIGN KEY (usuario2_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3247 (class 2606 OID 24688)
-- Name: mensagens mensagens_match_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mensagens
    ADD CONSTRAINT mensagens_match_id_fkey FOREIGN KEY (match_id) REFERENCES public.matches(id) ON DELETE CASCADE;


--
-- TOC entry 3248 (class 2606 OID 24693)
-- Name: mensagens mensagens_remetente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.mensagens
    ADD CONSTRAINT mensagens_remetente_id_fkey FOREIGN KEY (remetente_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3249 (class 2606 OID 32778)
-- Name: password_resets password_resets_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


-- Completed on 2025-06-11 12:32:21 -03

--
-- PostgreSQL database dump complete
--

