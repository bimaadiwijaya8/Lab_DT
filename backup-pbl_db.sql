--
-- PostgreSQL database dump
--

\restrict nbWXK4sWeIQBrrhr7FbwGQfSjcySytNL78zXSBgpN3lXfquQ1NqWiIUWTE8NoZs

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-12-11 03:21:32

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
-- TOC entry 243 (class 1255 OID 18268)
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
-- TOC entry 244 (class 1255 OID 18269)
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
-- TOC entry 245 (class 1255 OID 18270)
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
-- TOC entry 246 (class 1255 OID 18271)
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
-- TOC entry 247 (class 1255 OID 18272)
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
-- TOC entry 214 (class 1259 OID 18273)
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
-- TOC entry 215 (class 1259 OID 18278)
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
-- TOC entry 3498 (class 0 OID 0)
-- Dependencies: 215
-- Name: agenda_id_agenda_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.agenda_id_agenda_seq OWNED BY public.agenda.id_agenda;


--
-- TOC entry 216 (class 1259 OID 18279)
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
-- TOC entry 217 (class 1259 OID 18284)
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
-- TOC entry 3499 (class 0 OID 0)
-- Dependencies: 217
-- Name: anggota_id_anggota_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.anggota_id_anggota_seq OWNED BY public.anggota.id_anggota;


--
-- TOC entry 218 (class 1259 OID 18285)
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
    aksi character varying(20) DEFAULT 'pending'::character varying,
    status character varying(20) DEFAULT 'pending'::character varying
);


ALTER TABLE public.berita OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 18293)
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
-- TOC entry 3500 (class 0 OID 0)
-- Dependencies: 219
-- Name: berita_id_berita_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.berita_id_berita_seq OWNED BY public.berita.id_berita;


--
-- TOC entry 220 (class 1259 OID 18294)
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
-- TOC entry 221 (class 1259 OID 18299)
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
-- TOC entry 3501 (class 0 OID 0)
-- Dependencies: 221
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.fasilitas_id_fasilitas_seq OWNED BY public.fasilitas.id_fasilitas;


--
-- TOC entry 222 (class 1259 OID 18300)
-- Name: galeri; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.galeri (
    id_foto integer NOT NULL,
    nama_foto character varying(100),
    deskripsi text,
    file_foto text,
    id_anggota integer,
    updated_by integer
);


ALTER TABLE public.galeri OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 18305)
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
-- TOC entry 3502 (class 0 OID 0)
-- Dependencies: 223
-- Name: galeri_id_foto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.galeri_id_foto_seq OWNED BY public.galeri.id_foto;


--
-- TOC entry 224 (class 1259 OID 18306)
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
-- TOC entry 225 (class 1259 OID 18311)
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
-- TOC entry 3503 (class 0 OID 0)
-- Dependencies: 225
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jurnal_id_jurnal_seq OWNED BY public.jurnal.id_jurnal;


--
-- TOC entry 226 (class 1259 OID 18312)
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
-- TOC entry 227 (class 1259 OID 18317)
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
-- TOC entry 3504 (class 0 OID 0)
-- Dependencies: 227
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kerjasama_id_kerjasama_seq OWNED BY public.kerjasama.id_kerjasama;


--
-- TOC entry 228 (class 1259 OID 18318)
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
-- TOC entry 229 (class 1259 OID 18323)
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
-- TOC entry 3505 (class 0 OID 0)
-- Dependencies: 229
-- Name: kontak_id_kontak_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kontak_id_kontak_seq OWNED BY public.kontak.id_kontak;


--
-- TOC entry 230 (class 1259 OID 18324)
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
-- TOC entry 231 (class 1259 OID 18330)
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
-- TOC entry 3506 (class 0 OID 0)
-- Dependencies: 231
-- Name: member_id_member_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.member_id_member_seq OWNED BY public.member.id_member;


--
-- TOC entry 232 (class 1259 OID 18331)
-- Name: pengumuman; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengumuman (
    id_pengumuman integer NOT NULL,
    judul character varying(200),
    informasi text,
    id_anggota integer,
    tanggal date
);


ALTER TABLE public.pengumuman OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 18336)
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
-- TOC entry 3507 (class 0 OID 0)
-- Dependencies: 233
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengumuman_id_pengumuman_seq OWNED BY public.pengumuman.id_pengumuman;


--
-- TOC entry 234 (class 1259 OID 18337)
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
    id_anggota integer
);


ALTER TABLE public.publikasi OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 18344)
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
-- TOC entry 3508 (class 0 OID 0)
-- Dependencies: 235
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.publikasi_id_publikasi_seq OWNED BY public.publikasi.id_publikasi;


--
-- TOC entry 236 (class 1259 OID 18345)
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
-- TOC entry 237 (class 1259 OID 18351)
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
-- TOC entry 3509 (class 0 OID 0)
-- Dependencies: 237
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- TOC entry 238 (class 1259 OID 18352)
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
-- TOC entry 239 (class 1259 OID 18360)
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
-- TOC entry 3510 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 240 (class 1259 OID 18361)
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
-- TOC entry 241 (class 1259 OID 18365)
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
-- TOC entry 242 (class 1259 OID 18370)
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
-- TOC entry 3250 (class 2604 OID 18523)
-- Name: agenda id_agenda; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda ALTER COLUMN id_agenda SET DEFAULT nextval('public.agenda_id_agenda_seq'::regclass);


--
-- TOC entry 3251 (class 2604 OID 18524)
-- Name: anggota id_anggota; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota ALTER COLUMN id_anggota SET DEFAULT nextval('public.anggota_id_anggota_seq'::regclass);


--
-- TOC entry 3252 (class 2604 OID 18525)
-- Name: berita id_berita; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita ALTER COLUMN id_berita SET DEFAULT nextval('public.berita_id_berita_seq'::regclass);


--
-- TOC entry 3256 (class 2604 OID 18526)
-- Name: fasilitas id_fasilitas; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas ALTER COLUMN id_fasilitas SET DEFAULT nextval('public.fasilitas_id_fasilitas_seq'::regclass);


--
-- TOC entry 3257 (class 2604 OID 18527)
-- Name: galeri id_foto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri ALTER COLUMN id_foto SET DEFAULT nextval('public.galeri_id_foto_seq'::regclass);


--
-- TOC entry 3258 (class 2604 OID 18528)
-- Name: jurnal id_jurnal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal ALTER COLUMN id_jurnal SET DEFAULT nextval('public.jurnal_id_jurnal_seq'::regclass);


--
-- TOC entry 3259 (class 2604 OID 18529)
-- Name: kerjasama id_kerjasama; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama ALTER COLUMN id_kerjasama SET DEFAULT nextval('public.kerjasama_id_kerjasama_seq'::regclass);


--
-- TOC entry 3260 (class 2604 OID 18530)
-- Name: kontak id_kontak; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak ALTER COLUMN id_kontak SET DEFAULT nextval('public.kontak_id_kontak_seq'::regclass);


--
-- TOC entry 3261 (class 2604 OID 18531)
-- Name: member id_member; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member ALTER COLUMN id_member SET DEFAULT nextval('public.member_id_member_seq'::regclass);


--
-- TOC entry 3262 (class 2604 OID 18532)
-- Name: pengumuman id_pengumuman; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman ALTER COLUMN id_pengumuman SET DEFAULT nextval('public.pengumuman_id_pengumuman_seq'::regclass);


--
-- TOC entry 3263 (class 2604 OID 18533)
-- Name: publikasi id_publikasi; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi ALTER COLUMN id_publikasi SET DEFAULT nextval('public.publikasi_id_publikasi_seq'::regclass);


--
-- TOC entry 3266 (class 2604 OID 18534)
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- TOC entry 3268 (class 2604 OID 18535)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3467 (class 0 OID 18273)
-- Dependencies: 214
-- Data for Name: agenda; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.agenda (id_agenda, nama_agenda, tgl_agenda, link_agenda, id_anggota) FROM stdin;
1	Seminar Nasional Robotika Cerdas	2026-01-15	https://bit.ly/seminar-robotika-jan	1
2	Rapat Koordinasi Mingguan Staf Lab	2025-12-10	https://meet.google.com/rapat-lab-des	2
3	Pelatihan Penggunaan Software CAD	2026-01-22	https://forms.gle/pelatihan-cad-2026	3
4	agenda1	2026-01-17	youtube.com	2
\.


--
-- TOC entry 3469 (class 0 OID 18279)
-- Dependencies: 216
-- Data for Name: anggota; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.anggota (id_anggota, nama_gelar, foto, jabatan, email, no_telp, bidang_keahlian) FROM stdin;
1	Dr. Ir. Taufik Hidayat, M.T.	foto_taufik.jpg	Kepala Laboratorium	taufik.h@univ.ac.id	081122334455	Robotika, AI
2	Budi Santoso, S.Kom., M.Cs.	foto_budi.jpg	Staf Peneliti	budi.s@lab.id	081234567890	Pemrosesan Citra
3	Citra Dewi, S.T., M.Eng.	foto_citra.jpg	Asisten Laboratorium	citra.d@lab.id	085098765432	Machine Learning
\.


--
-- TOC entry 3471 (class 0 OID 18285)
-- Dependencies: 218
-- Data for Name: berita; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.berita (id_berita, judul, gambar, informasi, tanggal, author, created_at, aksi, status) FROM stdin;
1	Workshop Dasar-Dasar ROS	gambar_workshop.jpg	Telah diselenggarakan Workshop Dasar-Dasar Robot Operating System (ROS) pada 15-16 November 2025.	2025-11-17	1	2025-12-09 07:49:26.986257	approved	approved
2	Lab Meraih Hibah Penelitian Baru	gambar_hibah.jpg	Peneliti Lab mendapatkan hibah riset untuk 3 tahun ke depan dengan fokus IoT dan Data Science.	2025-10-25	2	2025-12-09 07:49:26.986257	approved	approved
3	Kunjungan Industri ke PT. Robotik Jaya	gambar_kunjungan.jpg	Mahasiswa dan Anggota Lab melakukan kunjungan ke PT. Robotik Jaya untuk melihat langsung proses produksi.	2025-11-01	3	2025-12-09 07:49:26.986257	approved	approved
4	judul	gambar_workshop.jpg	xxy	2025-11-15	1	2025-12-09 07:49:26.986257	approved	approved
5	judul1	fier	gambar.jpg	2025-11-22	2	2025-12-09 07:49:26.986257	approved	approved
11	tst	assets/img/berita/berita_1765285599_69381edfb0b34.png	tst	2025-11-19	1	2025-12-09 20:06:39.776715	rejected	rejected
10	test5	assets/img/berita/berita_1765252756_69379e94a137e.png	info test5	2025-12-14	1	2025-12-09 10:59:16.699622	approved	approved
9	et4	assets/img/berita/berita_1765244177_69377d11682dc.png	4646	2025-11-13	1	2025-12-09 08:36:17.452733	approved	approved
13	13	assets/img/berita/berita_1765336940_6938e76c369e6.png	13	2025-12-16	1	2025-12-10 10:22:20.267678	approved	approved
15	15	\N	15	2025-11-04	1	2025-12-10 17:46:58.683089	pending	pending
16	test	\N	Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.	2025-11-16	1	2025-12-10 17:50:14.826091	pending	pending
18	Lorem ipsum dolor sit amet, consectetur elit, do   et . Ut ad , quis ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate	\N	2	2025-12-04	1	2025-12-10 17:52:22.778587	approved	approved
17	Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do 	\N	test	2025-11-14	1	2025-12-10 17:51:16.22263	rejected	rejected
19	TRY55	assets/img/berita/berita_1765397977_9374b5268ad1403d.jpeg	try	2025-12-05	1	2025-12-10 20:39:48.028817	pending	pending
\.


--
-- TOC entry 3473 (class 0 OID 18294)
-- Dependencies: 220
-- Data for Name: fasilitas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fasilitas (id_fasilitas, nama_fasilitas, deskripsi, foto, created_by) FROM stdin;
1	Robot Lengan Industri (5-DOF)	Robot lengan 5 derajat kebebasan untuk eksperimen kontrol gerak dan *pick-and-place*.	foto_robot_lengan.jpg	1
2	Server GPU Nvidia Tesla V100	Server berkapasitas tinggi untuk komputasi deep learning dan AI.	foto_server_gpu.jpg	2
3	Printer 3D FDM Skala Besar	Digunakan untuk prototyping cepat komponen robotika dan mekanik.	foto_printer_3d.jpg	3
\.


--
-- TOC entry 3475 (class 0 OID 18300)
-- Dependencies: 222
-- Data for Name: galeri; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.galeri (id_foto, nama_foto, deskripsi, file_foto, id_anggota, updated_by) FROM stdin;
\.


--
-- TOC entry 3477 (class 0 OID 18306)
-- Dependencies: 224
-- Data for Name: jurnal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jurnal (id_jurnal, judul, tanggal_upload, penyusun, link_jurnal) FROM stdin;
\.


--
-- TOC entry 3479 (class 0 OID 18312)
-- Dependencies: 226
-- Data for Name: kerjasama; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kerjasama (id_kerjasama, nama_perusahaan, proposal, contact_perusahaan, id_anggota) FROM stdin;
\.


--
-- TOC entry 3481 (class 0 OID 18318)
-- Dependencies: 228
-- Data for Name: kontak; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kontak (id_kontak, nama, email, no_telp, deskripsi_tujuan, opsi_kerjasama, id_anggota) FROM stdin;
\.


--
-- TOC entry 3483 (class 0 OID 18324)
-- Dependencies: 230
-- Data for Name: member; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.member (id_member, email, nama, foto, nim, jurusan, prodi, kelas, tahun_angkatan, no_telp, status, password) FROM stdin;
1	rani.p@mhs.id	Rani Permata	foto_rani.jpg	123001	Teknik Elektro	Informatika	TI-A	2022	089887766554	aktif	pass_hash_rani
2	doni.s@mhs.id	Doni Saputra	foto_doni.jpg	123002	Teknik Elektro	Informatika	TI-B	2021	081998877665	alumni	pass_hash_doni
3	sinta.a@mhs.id	Sinta Amelia	foto_sinta.jpg	456003	Teknik Mesin	Mekatronika	TM-C	2023	082887766554	luar_lab	pass_hash_sinta
\.


--
-- TOC entry 3485 (class 0 OID 18331)
-- Dependencies: 232
-- Data for Name: pengumuman; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pengumuman (id_pengumuman, judul, informasi, id_anggota, tanggal) FROM stdin;
1	Pendaftaran Asisten Lab Semester Genap 2026	Pendaftaran dibuka mulai 10 hingga 20 Desember 2025. Silakan kirim CV dan transkrip nilai ke email lab.	1	2025-01-02
2	Jadwal Review Proyek Akhir	Review proyek akhir untuk batch 2023 akan dilaksanakan pada minggu ketiga Januari 2026. Detail jadwal akan diumumkan melalui email.	2	2025-01-03
3	Penggunaan Ruangan Komputasi	Ruangan komputasi akan ditutup sementara pada tanggal 5 Desember 2025 untuk pemeliharaan sistem. Harap simpan pekerjaan Anda sebelum tanggal tersebut.	3	2025-01-05
\.


--
-- TOC entry 3487 (class 0 OID 18337)
-- Dependencies: 234
-- Data for Name: publikasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.publikasi (id_publikasi, judul, penulis, tanggal_terbit, file_publikasi, deskripsi, created_at, updated_at, id_anggota) FROM stdin;
1	Implementasi Deep Learning untuk Deteksi Objek	Rani Permata, Taufik Hidayat	2025-10-01	file_publikasi_10.pdf	Jurnal mengenai model CNN untuk deteksi objek secara real-time.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	1
2	Analisis Performa Sistem Kontrol PID	Doni Saputra, Budi Santoso	2024-05-15	file_publikasi_11.pdf	Skripsi alumni tentang optimasi kontrol PID pada lengan robot.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	2
3	Review Metode Sensor Fusion	Sinta Amelia, Citra Dewi	2025-11-20	file_publikasi_12.pdf	Makalah review tentang berbagai teknik fusi data sensor.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	3
\.


--
-- TOC entry 3489 (class 0 OID 18345)
-- Dependencies: 236
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.settings (id, key, value, updated_at, updated_by) FROM stdin;
1	logo_path	assets/img/logo.png	2025-12-07 12:59:43.716939	\N
\.


--
-- TOC entry 3491 (class 0 OID 18352)
-- Dependencies: 238
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, username, password, nama, email, no_telp, role, created_at, updated_at) FROM stdin;
1	admin_lab	hashed_password_1	Budi Santoso	budi.s@lab.id	081234567890	admin	2025-12-04 00:41:14.876157	2025-12-04 00:41:14.876157
2	editor_konten	hashed_password_2	Citra Dewi	citra.d@lab.id	085098765432	editor	2025-12-04 00:41:14.876157	2025-12-04 00:41:14.876157
3	editor_galeri	hashed_password_3	Agus Salim	agus.s@lab.id	087112233445	editor	2025-12-04 00:41:14.876157	2025-12-04 00:41:14.876157
\.


--
-- TOC entry 3511 (class 0 OID 0)
-- Dependencies: 215
-- Name: agenda_id_agenda_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.agenda_id_agenda_seq', 3, true);


--
-- TOC entry 3512 (class 0 OID 0)
-- Dependencies: 217
-- Name: anggota_id_anggota_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.anggota_id_anggota_seq', 3, true);


--
-- TOC entry 3513 (class 0 OID 0)
-- Dependencies: 219
-- Name: berita_id_berita_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.berita_id_berita_seq', 21, true);


--
-- TOC entry 3514 (class 0 OID 0)
-- Dependencies: 221
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fasilitas_id_fasilitas_seq', 3, true);


--
-- TOC entry 3515 (class 0 OID 0)
-- Dependencies: 223
-- Name: galeri_id_foto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.galeri_id_foto_seq', 1, false);


--
-- TOC entry 3516 (class 0 OID 0)
-- Dependencies: 225
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jurnal_id_jurnal_seq', 1, false);


--
-- TOC entry 3517 (class 0 OID 0)
-- Dependencies: 227
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kerjasama_id_kerjasama_seq', 1, false);


--
-- TOC entry 3518 (class 0 OID 0)
-- Dependencies: 229
-- Name: kontak_id_kontak_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kontak_id_kontak_seq', 1, false);


--
-- TOC entry 3519 (class 0 OID 0)
-- Dependencies: 231
-- Name: member_id_member_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.member_id_member_seq', 3, true);


--
-- TOC entry 3520 (class 0 OID 0)
-- Dependencies: 233
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pengumuman_id_pengumuman_seq', 3, true);


--
-- TOC entry 3521 (class 0 OID 0)
-- Dependencies: 235
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.publikasi_id_publikasi_seq', 3, true);


--
-- TOC entry 3522 (class 0 OID 0)
-- Dependencies: 237
-- Name: settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.settings_id_seq', 1, true);


--
-- TOC entry 3523 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- TOC entry 3274 (class 2606 OID 18388)
-- Name: agenda agenda_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (id_agenda);


--
-- TOC entry 3276 (class 2606 OID 18390)
-- Name: anggota anggota_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota
    ADD CONSTRAINT anggota_pkey PRIMARY KEY (id_anggota);


--
-- TOC entry 3278 (class 2606 OID 18392)
-- Name: berita berita_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT berita_pkey PRIMARY KEY (id_berita);


--
-- TOC entry 3280 (class 2606 OID 18394)
-- Name: fasilitas fasilitas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fasilitas_pkey PRIMARY KEY (id_fasilitas);


--
-- TOC entry 3282 (class 2606 OID 18396)
-- Name: galeri galeri_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT galeri_pkey PRIMARY KEY (id_foto);


--
-- TOC entry 3284 (class 2606 OID 18398)
-- Name: jurnal jurnal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT jurnal_pkey PRIMARY KEY (id_jurnal);


--
-- TOC entry 3286 (class 2606 OID 18400)
-- Name: kerjasama kerjasama_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama
    ADD CONSTRAINT kerjasama_pkey PRIMARY KEY (id_kerjasama);


--
-- TOC entry 3288 (class 2606 OID 18402)
-- Name: kontak kontak_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT kontak_pkey PRIMARY KEY (id_kontak);


--
-- TOC entry 3290 (class 2606 OID 18404)
-- Name: member member_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member
    ADD CONSTRAINT member_email_key UNIQUE (email);


--
-- TOC entry 3292 (class 2606 OID 18406)
-- Name: member member_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member
    ADD CONSTRAINT member_pkey PRIMARY KEY (id_member);


--
-- TOC entry 3294 (class 2606 OID 18408)
-- Name: pengumuman pengumuman_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT pengumuman_pkey PRIMARY KEY (id_pengumuman);


--
-- TOC entry 3296 (class 2606 OID 18410)
-- Name: publikasi publikasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT publikasi_pkey PRIMARY KEY (id_publikasi);


--
-- TOC entry 3298 (class 2606 OID 18412)
-- Name: settings settings_key_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_key UNIQUE (key);


--
-- TOC entry 3300 (class 2606 OID 18414)
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- TOC entry 3302 (class 2606 OID 18416)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3304 (class 2606 OID 18418)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3306 (class 2606 OID 18420)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 3308 (class 2606 OID 18421)
-- Name: berita berita_author_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT berita_author_fkey FOREIGN KEY (author) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3307 (class 2606 OID 18426)
-- Name: agenda fk_agenda_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda
    ADD CONSTRAINT fk_agenda_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3309 (class 2606 OID 18431)
-- Name: berita fk_berita_author; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT fk_berita_author FOREIGN KEY (author) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3310 (class 2606 OID 18436)
-- Name: fasilitas fk_fasilitas_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fk_fasilitas_user FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3311 (class 2606 OID 18441)
-- Name: galeri fk_galeri_admin; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_admin FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3312 (class 2606 OID 18446)
-- Name: galeri fk_galeri_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3313 (class 2606 OID 18451)
-- Name: jurnal fk_jurnal_penyusun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT fk_jurnal_penyusun FOREIGN KEY (penyusun) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3315 (class 2606 OID 18456)
-- Name: kerjasama fk_kerjasama_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama
    ADD CONSTRAINT fk_kerjasama_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3316 (class 2606 OID 18461)
-- Name: kontak fk_kontak_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT fk_kontak_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3317 (class 2606 OID 18466)
-- Name: pengumuman fk_pengumuman_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT fk_pengumuman_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3318 (class 2606 OID 18471)
-- Name: publikasi fk_publikasi_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3319 (class 2606 OID 18476)
-- Name: publikasi fk_publikasi_member; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_member FOREIGN KEY (id_anggota) REFERENCES public.member(id_member) ON DELETE SET NULL;


--
-- TOC entry 3321 (class 2606 OID 18481)
-- Name: settings fk_settings_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT fk_settings_user FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3314 (class 2606 OID 18486)
-- Name: jurnal jurnal_penyusun_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT jurnal_penyusun_fkey FOREIGN KEY (penyusun) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3320 (class 2606 OID 18491)
-- Name: publikasi publikasi_id_anggota_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT publikasi_id_anggota_fkey FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


-- Completed on 2025-12-11 03:21:32

--
-- PostgreSQL database dump complete
--

\unrestrict nbWXK4sWeIQBrrhr7FbwGQfSjcySytNL78zXSBgpN3lXfquQ1NqWiIUWTE8NoZs

