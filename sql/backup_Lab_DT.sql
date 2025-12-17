--
-- PostgreSQL database dump
--

\restrict QXi4ilAkeIlB8bL0J6yDh3ahY09ewEiSTCHct1K1rBhVAXkJFMBe0iEjC2Tfb2O

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-12-18 01:40:00

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 243 (class 1255 OID 19883)
-- Name: add_fasilitas(character varying, text, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.add_fasilitas(p_nama character varying, p_deskripsi text, p_foto text, p_user_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO fasilitas(
        nama_fasilitas,
        deskripsi,
        foto,
        created_by,
        created_at,
        updated_at
    )
    VALUES(
        p_nama,
        p_deskripsi,
        p_foto,
        p_user_id,
        NOW(),
        NOW()
    );
END;
$$;


ALTER FUNCTION public.add_fasilitas(p_nama character varying, p_deskripsi text, p_foto text, p_user_id integer) OWNER TO postgres;

--
-- TOC entry 244 (class 1255 OID 19884)
-- Name: tambah_fasilitas(character varying, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.tambah_fasilitas(p_nama character varying, p_gambar character varying, p_iduser integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO fasilitas (nama, gambar, iduser)
    VALUES (p_nama, p_gambar, p_iduser);
END;
$$;


ALTER FUNCTION public.tambah_fasilitas(p_nama character varying, p_gambar character varying, p_iduser integer) OWNER TO postgres;

--
-- TOC entry 245 (class 1255 OID 19885)
-- Name: update_galeri(integer, character varying, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_galeri(p_idgaleri integer, p_nama character varying, p_gambar character varying, p_iduser integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE galeri
    SET nama = p_nama,
        gambar = p_gambar,
        updated_by = p_iduser,
        updated_at = NOW()
    WHERE idgaleri = p_idgaleri;
END;
$$;


ALTER FUNCTION public.update_galeri(p_idgaleri integer, p_nama character varying, p_gambar character varying, p_iduser integer) OWNER TO postgres;

--
-- TOC entry 246 (class 1255 OID 19886)
-- Name: update_galeri(integer, character varying, text, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_galeri(p_id_foto integer, p_nama character varying, p_deskripsi text, p_file text, p_user_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE galeri
    SET 
        nama_foto = p_nama,
        deskripsi = p_deskripsi,
        file_foto = p_file,
        updated_at = NOW(),
        updated_by = p_user_id
    WHERE id_foto = p_id_foto;
END;
$$;


ALTER FUNCTION public.update_galeri(p_id_foto integer, p_nama character varying, p_deskripsi text, p_file text, p_user_id integer) OWNER TO postgres;

--
-- TOC entry 247 (class 1255 OID 19887)
-- Name: update_setting(character varying, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_setting(p_key character varying, p_value text, p_user_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE settings
    SET value = p_value,
        updated_at = NOW(),
        updated_by = p_user_id
    WHERE key = p_key;
END;
$$;


ALTER FUNCTION public.update_setting(p_key character varying, p_value text, p_user_id integer) OWNER TO postgres;

--
-- TOC entry 248 (class 1255 OID 19888)
-- Name: update_settings(integer, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_settings(p_idsettings integer, p_value text, p_iduser integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE settings
    SET value = p_value,
        updatetime = NOW(),
        iduser = p_iduser
    WHERE idsettings = p_idsettings;
END;
$$;


ALTER FUNCTION public.update_settings(p_idsettings integer, p_value text, p_iduser integer) OWNER TO postgres;

--
-- TOC entry 249 (class 1255 OID 19889)
-- Name: update_timestamp(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_timestamp() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_timestamp() OWNER TO postgres;

--
-- TOC entry 250 (class 1255 OID 19890)
-- Name: upload_publikasi(integer, character varying, text, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.upload_publikasi(p_idmember integer, p_judul character varying, p_konten text, p_gambar character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO publikasi (idmember, judul, konten, gambar)
    VALUES (p_idmember, p_judul, p_konten, p_gambar);
END;
$$;


ALTER FUNCTION public.upload_publikasi(p_idmember integer, p_judul character varying, p_konten text, p_gambar character varying) OWNER TO postgres;

--
-- TOC entry 251 (class 1255 OID 19891)
-- Name: upload_publikasi(integer, character varying, character varying, date, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.upload_publikasi(p_id_member integer, p_judul character varying, p_penulis character varying, p_tanggal date, p_file text, p_deskripsi text) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO publikasi(
        id_anggota,
        judul,
        penulis,
        tanggal_terbit,
        file_publikasi,
        deskripsi,
        created_at,
        updated_at
    )
    VALUES (
        p_id_member,
        p_judul,
        p_penulis,
        p_tanggal,
        p_file,
        p_deskripsi,
        NOW(),
        NOW()
    );
END;
$$;


ALTER FUNCTION public.upload_publikasi(p_id_member integer, p_judul character varying, p_penulis character varying, p_tanggal date, p_file text, p_deskripsi text) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 214 (class 1259 OID 19892)
-- Name: agenda; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.agenda (
    id_agenda integer NOT NULL,
    nama_agenda character varying(200),
    tgl_agenda date,
    link_agenda text,
    id_anggota integer
);


ALTER TABLE public.agenda OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 19897)
-- Name: agenda_id_agenda_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.agenda_id_agenda_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.agenda_id_agenda_seq OWNER TO postgres;

--
-- TOC entry 3500 (class 0 OID 0)
-- Dependencies: 215
-- Name: agenda_id_agenda_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.agenda_id_agenda_seq OWNED BY public.agenda.id_agenda;


--
-- TOC entry 216 (class 1259 OID 19898)
-- Name: anggota; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.anggota (
    id_anggota integer NOT NULL,
    nama_gelar character varying(100),
    foto text,
    jabatan character varying(100),
    email character varying(100),
    no_telp character varying(20),
    bidang_keahlian character varying(200)
);


ALTER TABLE public.anggota OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 19903)
-- Name: anggota_id_anggota_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.anggota_id_anggota_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.anggota_id_anggota_seq OWNER TO postgres;

--
-- TOC entry 3501 (class 0 OID 0)
-- Dependencies: 217
-- Name: anggota_id_anggota_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.anggota_id_anggota_seq OWNED BY public.anggota.id_anggota;


--
-- TOC entry 218 (class 1259 OID 19904)
-- Name: berita; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.berita (
    id_berita integer NOT NULL,
    judul character varying(200),
    gambar text,
    informasi text,
    tanggal date,
    author integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    status character varying(20) DEFAULT 'pending'::character varying
);


ALTER TABLE public.berita OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 19911)
-- Name: berita_id_berita_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.berita_id_berita_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.berita_id_berita_seq OWNER TO postgres;

--
-- TOC entry 3502 (class 0 OID 0)
-- Dependencies: 219
-- Name: berita_id_berita_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.berita_id_berita_seq OWNED BY public.berita.id_berita;


--
-- TOC entry 220 (class 1259 OID 19912)
-- Name: fasilitas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fasilitas (
    id_fasilitas integer NOT NULL,
    nama_fasilitas character varying(100),
    deskripsi text,
    foto text,
    created_by integer
);


ALTER TABLE public.fasilitas OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 19917)
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.fasilitas_id_fasilitas_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.fasilitas_id_fasilitas_seq OWNER TO postgres;

--
-- TOC entry 3503 (class 0 OID 0)
-- Dependencies: 221
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.fasilitas_id_fasilitas_seq OWNED BY public.fasilitas.id_fasilitas;


--
-- TOC entry 222 (class 1259 OID 19918)
-- Name: galeri; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.galeri (
    id_foto integer NOT NULL,
    nama_foto character varying(100),
    deskripsi text,
    file_foto text,
    id_anggota integer,
    updated_by integer,
    status character varying(20)
);


ALTER TABLE public.galeri OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 19923)
-- Name: galeri_id_foto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.galeri_id_foto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.galeri_id_foto_seq OWNER TO postgres;

--
-- TOC entry 3504 (class 0 OID 0)
-- Dependencies: 223
-- Name: galeri_id_foto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.galeri_id_foto_seq OWNED BY public.galeri.id_foto;


--
-- TOC entry 224 (class 1259 OID 19924)
-- Name: jurnal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jurnal (
    id_jurnal integer NOT NULL,
    judul character varying(200),
    tanggal_upload date,
    penyusun integer,
    link_jurnal text
);


ALTER TABLE public.jurnal OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 19929)
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jurnal_id_jurnal_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jurnal_id_jurnal_seq OWNER TO postgres;

--
-- TOC entry 3505 (class 0 OID 0)
-- Dependencies: 225
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jurnal_id_jurnal_seq OWNED BY public.jurnal.id_jurnal;


--
-- TOC entry 226 (class 1259 OID 19930)
-- Name: kerjasama; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kerjasama (
    id_kerjasama integer NOT NULL,
    nama_perusahaan character varying(100),
    proposal text,
    contact_perusahaan character varying(100),
    id_anggota integer
);


ALTER TABLE public.kerjasama OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 19935)
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kerjasama_id_kerjasama_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kerjasama_id_kerjasama_seq OWNER TO postgres;

--
-- TOC entry 3506 (class 0 OID 0)
-- Dependencies: 227
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kerjasama_id_kerjasama_seq OWNED BY public.kerjasama.id_kerjasama;


--
-- TOC entry 228 (class 1259 OID 19936)
-- Name: kontak; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kontak (
    id_kontak integer NOT NULL,
    nama character varying(100),
    email character varying(100),
    no_telp character varying(20),
    deskripsi_tujuan text,
    opsi_kerjasama character varying(100),
    id_anggota integer
);


ALTER TABLE public.kontak OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 19941)
-- Name: kontak_id_kontak_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kontak_id_kontak_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kontak_id_kontak_seq OWNER TO postgres;

--
-- TOC entry 3507 (class 0 OID 0)
-- Dependencies: 229
-- Name: kontak_id_kontak_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kontak_id_kontak_seq OWNED BY public.kontak.id_kontak;


--
-- TOC entry 230 (class 1259 OID 19942)
-- Name: member; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.member (
    id_member integer NOT NULL,
    email character varying(100),
    nama character varying(100),
    foto text,
    nim character varying(20),
    jurusan character varying(100),
    prodi character varying(100),
    kelas character varying(50),
    tahun_angkatan integer,
    no_telp character varying(20),
    status character varying(20),
    password character varying(255) NOT NULL,
    CONSTRAINT member_status_check CHECK (((status)::text = ANY (ARRAY[('aktif'::character varying)::text, ('alumni'::character varying)::text, ('luar_lab'::character varying)::text])))
);


ALTER TABLE public.member OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 19948)
-- Name: member_id_member_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.member_id_member_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.member_id_member_seq OWNER TO postgres;

--
-- TOC entry 3508 (class 0 OID 0)
-- Dependencies: 231
-- Name: member_id_member_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.member_id_member_seq OWNED BY public.member.id_member;


--
-- TOC entry 232 (class 1259 OID 19949)
-- Name: pengumuman; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengumuman (
    id_pengumuman integer NOT NULL,
    judul character varying(200),
    informasi text,
    id_anggota integer,
    tanggal date,
    status character varying(20)
);


ALTER TABLE public.pengumuman OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 19954)
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengumuman_id_pengumuman_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pengumuman_id_pengumuman_seq OWNER TO postgres;

--
-- TOC entry 3509 (class 0 OID 0)
-- Dependencies: 233
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengumuman_id_pengumuman_seq OWNED BY public.pengumuman.id_pengumuman;


--
-- TOC entry 234 (class 1259 OID 19955)
-- Name: publikasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.publikasi (
    id_publikasi integer NOT NULL,
    judul character varying(200) NOT NULL,
    penulis character varying(200) NOT NULL,
    tanggal_terbit date NOT NULL,
    file_publikasi text,
    deskripsi text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    id_anggota integer,
    status character varying(20),
    id_member integer
);


ALTER TABLE public.publikasi OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 19962)
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.publikasi_id_publikasi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.publikasi_id_publikasi_seq OWNER TO postgres;

--
-- TOC entry 3510 (class 0 OID 0)
-- Dependencies: 235
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.publikasi_id_publikasi_seq OWNED BY public.publikasi.id_publikasi;


--
-- TOC entry 236 (class 1259 OID 19963)
-- Name: settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.settings (
    id integer NOT NULL,
    key character varying(100) NOT NULL,
    value text NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_by integer
);


ALTER TABLE public.settings OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 19969)
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.settings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.settings_id_seq OWNER TO postgres;

--
-- TOC entry 3511 (class 0 OID 0)
-- Dependencies: 237
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- TOC entry 238 (class 1259 OID 19970)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    nama character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    no_telp character varying(20),
    role character varying(20) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT users_role_check CHECK (((role)::text = ANY (ARRAY[('admin'::character varying)::text, ('editor'::character varying)::text])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 19978)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 3512 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 240 (class 1259 OID 19979)
-- Name: vw_galeri_users; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_galeri_users AS
 SELECT g.id_foto,
    g.nama_foto,
    g.nama_foto AS judul_foto,
    g.deskripsi,
    g.file_foto,
    g.id_anggota,
    u.id AS user_id,
    u.nama AS updated_by,
    u.role
   FROM (public.galeri g
     LEFT JOIN public.users u ON ((g.id_anggota = u.id)));


ALTER TABLE public.vw_galeri_users OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 19983)
-- Name: vw_publikasi_member; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_publikasi_member AS
 SELECT p.id_publikasi,
    p.judul,
    p.penulis,
    p.tanggal_terbit,
    p.file_publikasi,
    p.deskripsi,
    p.created_at,
    p.updated_at,
    m.id_member,
    m.nama AS nama_member,
    m.email AS email_member,
    m.jurusan,
    m.prodi,
    m.tahun_angkatan
   FROM (public.publikasi p
     LEFT JOIN public.member m ON ((p.id_anggota = m.id_member)));


ALTER TABLE public.vw_publikasi_member OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 19988)
-- Name: vw_settings_users; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_settings_users AS
 SELECT s.id,
    s.key,
    s.value,
    s.updated_at,
    u.id AS user_id,
    u.nama AS user_name,
    u.role
   FROM (public.settings s
     LEFT JOIN public.users u ON ((s.updated_by = u.id)));


ALTER TABLE public.vw_settings_users OWNER TO postgres;

--
-- TOC entry 3254 (class 2604 OID 19992)
-- Name: agenda id_agenda; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda ALTER COLUMN id_agenda SET DEFAULT nextval('public.agenda_id_agenda_seq'::regclass);


--
-- TOC entry 3255 (class 2604 OID 19993)
-- Name: anggota id_anggota; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota ALTER COLUMN id_anggota SET DEFAULT nextval('public.anggota_id_anggota_seq'::regclass);


--
-- TOC entry 3256 (class 2604 OID 19994)
-- Name: berita id_berita; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita ALTER COLUMN id_berita SET DEFAULT nextval('public.berita_id_berita_seq'::regclass);


--
-- TOC entry 3259 (class 2604 OID 19995)
-- Name: fasilitas id_fasilitas; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas ALTER COLUMN id_fasilitas SET DEFAULT nextval('public.fasilitas_id_fasilitas_seq'::regclass);


--
-- TOC entry 3260 (class 2604 OID 19996)
-- Name: galeri id_foto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri ALTER COLUMN id_foto SET DEFAULT nextval('public.galeri_id_foto_seq'::regclass);


--
-- TOC entry 3261 (class 2604 OID 19997)
-- Name: jurnal id_jurnal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal ALTER COLUMN id_jurnal SET DEFAULT nextval('public.jurnal_id_jurnal_seq'::regclass);


--
-- TOC entry 3262 (class 2604 OID 19998)
-- Name: kerjasama id_kerjasama; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama ALTER COLUMN id_kerjasama SET DEFAULT nextval('public.kerjasama_id_kerjasama_seq'::regclass);


--
-- TOC entry 3263 (class 2604 OID 19999)
-- Name: kontak id_kontak; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak ALTER COLUMN id_kontak SET DEFAULT nextval('public.kontak_id_kontak_seq'::regclass);


--
-- TOC entry 3264 (class 2604 OID 20000)
-- Name: member id_member; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member ALTER COLUMN id_member SET DEFAULT nextval('public.member_id_member_seq'::regclass);


--
-- TOC entry 3265 (class 2604 OID 20001)
-- Name: pengumuman id_pengumuman; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman ALTER COLUMN id_pengumuman SET DEFAULT nextval('public.pengumuman_id_pengumuman_seq'::regclass);


--
-- TOC entry 3266 (class 2604 OID 20002)
-- Name: publikasi id_publikasi; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi ALTER COLUMN id_publikasi SET DEFAULT nextval('public.publikasi_id_publikasi_seq'::regclass);


--
-- TOC entry 3269 (class 2604 OID 20003)
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- TOC entry 3271 (class 2604 OID 20004)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3469 (class 0 OID 19892)
-- Dependencies: 214
-- Data for Name: agenda; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.agenda (id_agenda, nama_agenda, tgl_agenda, link_agenda, id_anggota) FROM stdin;
2	Rapat Koordinasi Mingguan Staf Lab	2025-12-10	https://meet.google.com/rapat-lab-des	2
3	Pelatihan Penggunaan Software CAD	2026-01-22	https://forms.gle/pelatihan-cad-2026	3
4	agenda1	2026-01-17	youtube.com	2
6	agenda3	2025-11-13	https://www.youtube.com/	4
5	agenda2	2025-11-12	https://bit.ly/seminar-robotika-jan	3
11	AGENDABRU	2025-11-12	https://bit.ly/seminar-robotika-jan	2
1	Seminar Nasional Robotika Cerdas	2026-01-15	https://bit.ly/seminar-robotika-jan	\N
7	rer34	2025-12-04	https://chatgpt.com/	\N
9	533553	2025-12-05	https://chatgpt.com/	\N
10	23245	2025-12-23	https://chatgpt.com/	\N
12	A2323	2025-12-05	https://bit.ly/seminar-robotika-jan	\N
13	AA	2025-12-05	https://bit.ly/seminar-robotika-jan	\N
8	rer34	2025-12-04	https://chatgpt.com/	4
\.


--
-- TOC entry 3471 (class 0 OID 19898)
-- Dependencies: 216
-- Data for Name: anggota; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.anggota (id_anggota, nama_gelar, foto, jabatan, email, no_telp, bidang_keahlian) FROM stdin;
4	UXHDEWR		ITER432	TJEIR34@GMAIL.COM	T83U9423	TE8RJO34
3	Citra Dewi, S.T., M.Eng.	../assets/img/anggota/anggota_1765812755_fasilitas_1765669641_a02898ed40d7711990aef44916e905f8.jpg	Asisten Laboratorium	citra.d@lab.id	085098765432	Machine Learning
2	Ms. Eve	../assets/img/anggota/anggota_1765813246_be56a5f3e05bf1069ca08df2165b5e07.jpg	Staf Penelitr	budi.s@lab.id	081234567890	Pemrosesan Citra
5	EDIT	../assets/img/anggota/anggota_1765815033_135224471_p0.png	EDIT	EDIT@GMAIL.COM	EDIT	EDIT
10	Dr. Ir. Ahmad Subarjo, M.T.	ketua.jpg	Ketua	ahmad.subarjo@lab.id	081122334455	Sistem Kontrol & Robotika
11	Siti Aminah, S.Kom., M.Cs.	sekretaris.jpg	Sekretaris	siti.aminah@lab.id	081223344556	Kecerdasan Buatan
12	Budi Cahyono, S.T.	anggota1.jpg	Anggota	budi.c@lab.id	081334455667	Internet of Things
\.


--
-- TOC entry 3473 (class 0 OID 19904)
-- Dependencies: 218
-- Data for Name: berita; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.berita (id_berita, judul, gambar, informasi, tanggal, author, created_at, status) FROM stdin;
45	IEJR3442	../assets/img/berita/be56a5f3e05bf1069ca08df2165b5e07.jpg	daee	2025-12-17	2	2025-12-14 02:45:48.511126	approved
47	UHRER	../assets/img/berita/1765666755_135224471_p0.png	ijerji-2	2025-12-13	2	2025-12-14 05:59:15.527441	approved
33	TRY5N	../assets/img/berita/Screenshot_2025-11-22_140415.png	ETERERTERIEREIR	2025-11-18	\N	2025-12-13 17:37:24.832957	rejected
49	MTY	../assets/img/berita/a02898ed40d7711990aef44916e905f8.jpg	MTY	2025-12-11	2	2025-12-16 18:01:16.64169	pending
\.


--
-- TOC entry 3475 (class 0 OID 19912)
-- Dependencies: 220
-- Data for Name: fasilitas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fasilitas (id_fasilitas, nama_fasilitas, deskripsi, foto, created_by) FROM stdin;
3	Printer 3D FDM Skala Besar SDD	Digunakan untuk prototyping cepat komponenSSD robotika dan mekanik.	../assets/img/fasilitas/fasilitas_1765669641_a02898ed40d7711990aef44916e905f8.jpg	3
2	Server GPU Nvidia Tesla V100	Server berkapasitas tinggi untuk komputasi deep learning dan AI.	../assets/img/fasilitas/fasilitas_1765669690_f2c75990a45f197d9f18c2c099f8751e.jpg	2
1	Robot Lengan Industri (5-DOF)	Robot lengan 5 derajat kebebasan untuk eksperimen kontrol gerak dan *pick-and-place*.	../assets/img/fasilitas/fasilitas_1765669702_135224471_p0.png	1
4	i23	RRIU32	../assets/img/fasilitas/fasilitas_1765670046_Screenshot_2025-11-21_011745.png	1
\.


--
-- TOC entry 3477 (class 0 OID 19918)
-- Dependencies: 222
-- Data for Name: galeri; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.galeri (id_foto, nama_foto, deskripsi, file_foto, id_anggota, updated_by, status) FROM stdin;
10	HJRU	4545	../assets/img/galeri/galeri_1765794301_a02898ed40d7711990aef44916e905f8.jpg	3	1	approved
5	IGaleri5	IGaleri5	../assets/img/galeri/galeri_1765723851_Screenshot_2025-11-22_183937.png	4	1	pending
3	iGaleri3	ietto	../assets/img/galeri/galeri_1765673943_Screenshot_2025-11-20_170828.png	2	2	approved
8	HJRUJH$#	423rr3wr	../assets/img/galeri/galeri_1765730619_f2c75990a45f197d9f18c2c099f8751e.jpg	4	1	approved
7	IGaleri6	RW	../assets/img/galeri/galeri_1765729721_135224471_p0.png	4	1	approved
\.


--
-- TOC entry 3479 (class 0 OID 19924)
-- Dependencies: 224
-- Data for Name: jurnal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jurnal (id_jurnal, judul, tanggal_upload, penyusun, link_jurnal) FROM stdin;
\.


--
-- TOC entry 3481 (class 0 OID 19930)
-- Dependencies: 226
-- Data for Name: kerjasama; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kerjasama (id_kerjasama, nama_perusahaan, proposal, contact_perusahaan, id_anggota) FROM stdin;
\.


--
-- TOC entry 3483 (class 0 OID 19936)
-- Dependencies: 228
-- Data for Name: kontak; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kontak (id_kontak, nama, email, no_telp, deskripsi_tujuan, opsi_kerjasama, id_anggota) FROM stdin;
\.


--
-- TOC entry 3485 (class 0 OID 19942)
-- Dependencies: 230
-- Data for Name: member; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.member (id_member, email, nama, foto, nim, jurusan, prodi, kelas, tahun_angkatan, no_telp, status, password) FROM stdin;
2	doni.s@mhs.id	Doni Saputra	foto_doni.jpg	123002	Teknik Elektro	Informatika	TI-B	2021	081998877665	alumni	pass_hash_doni
3	sinta.a@mhs.id	Sinta Amelia	foto_sinta.jpg	456003	Teknik Mesin	Mekatronika	TM-C	2023	082887766554	luar_lab	pass_hash_sinta
1	rani.p@mhs.id	Phrolova	../assets/img/profile/profile_1_1765883634.jpg	22222	Teknik Elektro	Informatika	TI-2G	2022	22222222	aktif	e10adc3949ba59abbe56e057f20f883e
4	bimaadiwijaya83@gmail.com	BimaAdiwijaya	../assets/img/member/member_1765994749_6942f0fd4da8a.jpg	244107020022	Teknik Informatika	TI	TI-2G	2024	087877088963	aktif	$2y$10$WfUFy0fahyjP434yu97gC.2Jm5qxOBVH4Tmm9tSeg/eRGBMcHZD5a
9	bimaadiwijaya93@gmail.com	Bima	../assets/img/member/member_1765995552_6942f42090b5f.jpg	24	Teknik Informatika	SIB	TI-2G	2024	087877088963	aktif	$2y$10$JXViNWX65lspHU56Yc.YRuWaV7g8bSaSZ4z2oNUOsU/5HznwdbXMK
12	bimaadiwijaya13@gmail.com	BimaA	../assets/img/member/member_1765995932_6942f59cecb62.jpg	244	Teknik Komputer dan Jaringan	TKJ	TI-2G	2023	08888888	aktif	$2y$10$i9Wr5E8d//qwwBPc2wIHSuMWLVNzztvS70w7tm0N87GjrQgjFWgDS
\.


--
-- TOC entry 3487 (class 0 OID 19949)
-- Dependencies: 232
-- Data for Name: pengumuman; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pengumuman (id_pengumuman, judul, informasi, id_anggota, tanggal, status) FROM stdin;
1	Pendaftaran Asisten Lab Semester Genap 2026	Pendaftaran dibuka mulai 10 hingga 20 Desember 2025. Silakan kirim CV dan transkrip nilai ke email lab.	\N	2025-01-02	pending
2	Jadwal Review Proyek Akhir	Review proyek akhir untuk batch 2023 akan dilaksanakan pada minggu ketiga Januari 2026. Detail jadwal akan diumumkan melalui email.	2	2025-01-03	pending
5	judul5	info judul5	\N	2025-01-01	pending
3	Penggunaan Ruangan Komputasi	Ruangan komputasi akan ditutup sementara pada tanggal 5 Desember 2025 untuk pemeliharaan sistem. Harap simpan pekerjaan Anda sebelum tanggal tersebut.	3	2025-01-05	approved
4	judul4	info judul4	2	2025-01-08	approved
9	EDIT2	EDIT	5	2025-12-12	pending
10	2323232	34343434	2	2025-12-16	approved
11	PENDING	PENDING	5	2025-12-12	approved
\.


--
-- TOC entry 3489 (class 0 OID 19955)
-- Dependencies: 234
-- Data for Name: publikasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.publikasi (id_publikasi, judul, penulis, tanggal_terbit, file_publikasi, deskripsi, created_at, updated_at, id_anggota, status, id_member) FROM stdin;
4	5JUDUL4	IEJRIER5	2025-12-02	../assets/files/publikasi/publikasi_1765793435_English_Assignment_EX10.pdf	EIJRIERrwr	2025-12-14 02:53:44.249846	2025-12-14 02:53:44.249846	\N	pending	1
3	Review Metode Sensor Fusion3	Sinta Amelia, Citra Dewi	2025-11-20	file_publikasi_12.pdf	Makalah review tentang berbagai teknik fusi data sensor.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	3	rejected	\N
2	Analisis Performa Sistem Kontrol PIDe	Doni Saputra, Budi Santoso	2024-05-01	file_publikasi_11.pdf	Skripsi alumni tentang optimasi kontrol PID pada lengan robot.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	2	pending	\N
12	iu394x	33i4j34	2025-12-16	../assets/files/publikasi/publikasi_1765855692_English_Assignment_EX10.pdf	2323	2025-12-16 10:28:12.128965	2025-12-16 10:28:12.128965	2	pending	\N
14	CRUD	CRUD	2025-12-12	../assets/files/publikasi/publikasi_1765883484_ENGLISH_U6.pdf	CRUD	2025-12-16 18:11:24.080816	2025-12-16 18:11:24.080816	3	approved	\N
20	EDT	EDT	2025-12-12	../assets/files/publikasi/publikasi_1765902769_publikasi_1765643036_BimaAdiwijaya_Polimorfisme.pdf	EDT	2025-12-16 23:32:30.86939	2025-12-16 23:32:30.86939	5	pending	\N
21	qqqq11	qqqq11	2025-12-16	../assets/files/publikasi/publikasi_1765902797_publikasi_1765793546_ENGLISH_U6.pdf	EEER	2025-12-16 23:33:17.48487	2025-12-16 23:33:17.48487	4	pending	\N
9	rwwewe	ewewewee	2025-12-05	../assets/files/publikasi/publikasi_1765793546_ENGLISH_U6.pdf	ewew4wewe	2025-12-15 17:12:07.920172	2025-12-15 17:12:07.920172	\N	approved	1
5	TRY5N	IEJRIER5	2025-12-10	../assets/files/publikasi/publikasi_1765785106_ENGLISH_U6.pdf	e42	2025-12-15 14:45:18.954999	2025-12-15 14:45:18.954999	\N	approved	1
\.


--
-- TOC entry 3491 (class 0 OID 19963)
-- Dependencies: 236
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.settings (id, key, value, updated_at, updated_by) FROM stdin;
1	logo_path	assets/img/logo.png	2025-12-07 12:59:43.716939	\N
\.


--
-- TOC entry 3493 (class 0 OID 19970)
-- Dependencies: 238
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, username, password, nama, email, no_telp, role, created_at, updated_at) FROM stdin;
2	editor_konten	hashed_password_2	Citra Dewi	citra.d@lab.id	085098765432	editor	2025-12-04 00:41:14.876157	2025-12-04 00:41:14.876157
3	editor_galeri	hashed_password_3	Agus Salim	agus.s@lab.id	087112233445	editor	2025-12-04 00:41:14.876157	2025-12-04 00:41:14.876157
1	admin_lab	3f7caa3d471688b704b73e9a77b1107f	Budi Santoso	budi.s@lab.id	081234567890	admin	2025-12-04 00:41:14.876157	2025-12-04 00:41:14.876157
4	tes_admin	$2y$12$9V32QC4BPpQRPdVweWG3puoXJHdaQkQ9Tj1b5ClipLfULcKimqCtG	tes_admin	tesadmin@gmail.com	tes_admin	admin	2025-12-17 00:41:58.796053	2025-12-17 00:41:58.796053
5	tes_editor	$2y$12$RoPz.bNCyZEkDAPVBVXsbuCVgnWJIpZtc010heeEyuiiQugoL1qAq	tes_editor	teseditor@gmail.com	teseditor	editor	2025-12-17 00:53:04.453197	2025-12-17 00:53:04.453197
\.


--
-- TOC entry 3513 (class 0 OID 0)
-- Dependencies: 215
-- Name: agenda_id_agenda_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.agenda_id_agenda_seq', 13, true);


--
-- TOC entry 3514 (class 0 OID 0)
-- Dependencies: 217
-- Name: anggota_id_anggota_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.anggota_id_anggota_seq', 12, true);


--
-- TOC entry 3515 (class 0 OID 0)
-- Dependencies: 219
-- Name: berita_id_berita_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.berita_id_berita_seq', 51, true);


--
-- TOC entry 3516 (class 0 OID 0)
-- Dependencies: 221
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fasilitas_id_fasilitas_seq', 6, true);


--
-- TOC entry 3517 (class 0 OID 0)
-- Dependencies: 223
-- Name: galeri_id_foto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.galeri_id_foto_seq', 11, true);


--
-- TOC entry 3518 (class 0 OID 0)
-- Dependencies: 225
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jurnal_id_jurnal_seq', 1, false);


--
-- TOC entry 3519 (class 0 OID 0)
-- Dependencies: 227
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kerjasama_id_kerjasama_seq', 1, false);


--
-- TOC entry 3520 (class 0 OID 0)
-- Dependencies: 229
-- Name: kontak_id_kontak_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kontak_id_kontak_seq', 1, false);


--
-- TOC entry 3521 (class 0 OID 0)
-- Dependencies: 231
-- Name: member_id_member_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.member_id_member_seq', 12, true);


--
-- TOC entry 3522 (class 0 OID 0)
-- Dependencies: 233
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pengumuman_id_pengumuman_seq', 12, true);


--
-- TOC entry 3523 (class 0 OID 0)
-- Dependencies: 235
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.publikasi_id_publikasi_seq', 22, true);


--
-- TOC entry 3524 (class 0 OID 0)
-- Dependencies: 237
-- Name: settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.settings_id_seq', 1, true);


--
-- TOC entry 3525 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- TOC entry 3277 (class 2606 OID 20006)
-- Name: agenda agenda_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (id_agenda);


--
-- TOC entry 3279 (class 2606 OID 20008)
-- Name: anggota anggota_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota
    ADD CONSTRAINT anggota_pkey PRIMARY KEY (id_anggota);


--
-- TOC entry 3281 (class 2606 OID 20010)
-- Name: berita berita_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT berita_pkey PRIMARY KEY (id_berita);


--
-- TOC entry 3283 (class 2606 OID 20012)
-- Name: fasilitas fasilitas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fasilitas_pkey PRIMARY KEY (id_fasilitas);


--
-- TOC entry 3285 (class 2606 OID 20014)
-- Name: galeri galeri_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT galeri_pkey PRIMARY KEY (id_foto);


--
-- TOC entry 3287 (class 2606 OID 20016)
-- Name: jurnal jurnal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT jurnal_pkey PRIMARY KEY (id_jurnal);


--
-- TOC entry 3289 (class 2606 OID 20018)
-- Name: kerjasama kerjasama_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama
    ADD CONSTRAINT kerjasama_pkey PRIMARY KEY (id_kerjasama);


--
-- TOC entry 3291 (class 2606 OID 20020)
-- Name: kontak kontak_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT kontak_pkey PRIMARY KEY (id_kontak);


--
-- TOC entry 3293 (class 2606 OID 20022)
-- Name: member member_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member
    ADD CONSTRAINT member_email_key UNIQUE (email);


--
-- TOC entry 3295 (class 2606 OID 20024)
-- Name: member member_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member
    ADD CONSTRAINT member_pkey PRIMARY KEY (id_member);


--
-- TOC entry 3297 (class 2606 OID 20026)
-- Name: pengumuman pengumuman_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT pengumuman_pkey PRIMARY KEY (id_pengumuman);


--
-- TOC entry 3299 (class 2606 OID 20028)
-- Name: publikasi publikasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT publikasi_pkey PRIMARY KEY (id_publikasi);


--
-- TOC entry 3301 (class 2606 OID 20030)
-- Name: settings settings_key_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_key UNIQUE (key);


--
-- TOC entry 3303 (class 2606 OID 20032)
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- TOC entry 3305 (class 2606 OID 20034)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3307 (class 2606 OID 20036)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3309 (class 2606 OID 20038)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 3311 (class 2606 OID 20039)
-- Name: berita berita_author_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT berita_author_fkey FOREIGN KEY (author) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3310 (class 2606 OID 20044)
-- Name: agenda fk_agenda_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda
    ADD CONSTRAINT fk_agenda_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3312 (class 2606 OID 20049)
-- Name: berita fk_berita_author; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT fk_berita_author FOREIGN KEY (author) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3313 (class 2606 OID 20054)
-- Name: fasilitas fk_fasilitas_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fk_fasilitas_user FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3314 (class 2606 OID 20059)
-- Name: galeri fk_galeri_admin; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_admin FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3315 (class 2606 OID 20064)
-- Name: galeri fk_galeri_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3316 (class 2606 OID 20069)
-- Name: jurnal fk_jurnal_penyusun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT fk_jurnal_penyusun FOREIGN KEY (penyusun) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3318 (class 2606 OID 20074)
-- Name: kerjasama fk_kerjasama_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama
    ADD CONSTRAINT fk_kerjasama_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3319 (class 2606 OID 20079)
-- Name: kontak fk_kontak_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT fk_kontak_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3320 (class 2606 OID 20084)
-- Name: pengumuman fk_pengumuman_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT fk_pengumuman_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3321 (class 2606 OID 20089)
-- Name: publikasi fk_publikasi_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3322 (class 2606 OID 20094)
-- Name: publikasi fk_publikasi_member; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_member FOREIGN KEY (id_member) REFERENCES public.member(id_member) ON DELETE SET NULL;


--
-- TOC entry 3323 (class 2606 OID 20099)
-- Name: settings fk_settings_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT fk_settings_user FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3317 (class 2606 OID 20104)
-- Name: jurnal jurnal_penyusun_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT jurnal_penyusun_fkey FOREIGN KEY (penyusun) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


-- Completed on 2025-12-18 01:40:01

--
-- PostgreSQL database dump complete
--

\unrestrict QXi4ilAkeIlB8bL0J6yDh3ahY09ewEiSTCHct1K1rBhVAXkJFMBe0iEjC2Tfb2O

