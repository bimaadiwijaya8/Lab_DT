--
-- PostgreSQL database dump
--

\restrict dUiHOFXgM1J7soEe1HDgYU4WIuPnxYG2Z8v1t15qTYwucLGJBrn1OcX7GHvo24w

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-12-18 20:46:15

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
-- TOC entry 245 (class 1255 OID 61052)
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
-- TOC entry 246 (class 1255 OID 61053)
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
-- TOC entry 247 (class 1255 OID 61054)
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
-- TOC entry 248 (class 1255 OID 61055)
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
-- TOC entry 249 (class 1255 OID 61056)
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
-- TOC entry 250 (class 1255 OID 61057)
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
-- TOC entry 251 (class 1255 OID 61058)
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
-- TOC entry 254 (class 1255 OID 61381)
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO postgres;

--
-- TOC entry 252 (class 1255 OID 61059)
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
-- TOC entry 253 (class 1255 OID 61060)
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
-- TOC entry 214 (class 1259 OID 61061)
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
-- TOC entry 215 (class 1259 OID 61066)
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
-- TOC entry 3558 (class 0 OID 0)
-- Dependencies: 215
-- Name: agenda_id_agenda_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.agenda_id_agenda_seq OWNED BY public.agenda.id_agenda;


--
-- TOC entry 216 (class 1259 OID 61067)
-- Name: anggota; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.anggota (
    id_anggota integer NOT NULL,
    nama_gelar character varying(100),
    foto text,
    jabatan character varying(100),
    email character varying(100),
    no_telp character varying(20),
    bidang_keahlian character varying(200),
    approval_status character varying(20) DEFAULT 'approved'::character varying,
    created_by integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    approved_by integer,
    approved_at timestamp without time zone,
    CONSTRAINT anggota_approval_status_check CHECK (((approval_status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.anggota OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 61072)
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
-- TOC entry 3559 (class 0 OID 0)
-- Dependencies: 217
-- Name: anggota_id_anggota_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.anggota_id_anggota_seq OWNED BY public.anggota.id_anggota;


--
-- TOC entry 218 (class 1259 OID 61073)
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
    status character varying(20) DEFAULT 'pending'::character varying,
    approval_status character varying(20) DEFAULT 'approved'::character varying,
    created_by integer,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    approved_by integer,
    approved_at timestamp without time zone,
    CONSTRAINT berita_approval_status_check CHECK (((approval_status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.berita OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 61080)
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
-- TOC entry 3560 (class 0 OID 0)
-- Dependencies: 219
-- Name: berita_id_berita_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.berita_id_berita_seq OWNED BY public.berita.id_berita;


--
-- TOC entry 220 (class 1259 OID 61081)
-- Name: fasilitas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fasilitas (
    id_fasilitas integer NOT NULL,
    nama_fasilitas character varying(100),
    deskripsi text,
    foto text,
    created_by integer,
    approval_status character varying(20) DEFAULT 'approved'::character varying,
    approved_by integer,
    approved_at timestamp without time zone,
    CONSTRAINT fasilitas_approval_status_check CHECK (((approval_status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.fasilitas OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 61086)
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
-- TOC entry 3561 (class 0 OID 0)
-- Dependencies: 221
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.fasilitas_id_fasilitas_seq OWNED BY public.fasilitas.id_fasilitas;


--
-- TOC entry 222 (class 1259 OID 61087)
-- Name: galeri; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.galeri (
    id_foto integer NOT NULL,
    nama_foto character varying(100),
    deskripsi text,
    file_foto text,
    id_anggota integer,
    updated_by integer,
    status character varying(20),
    approval_status character varying(20) DEFAULT 'approved'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    approved_by integer,
    approved_at timestamp without time zone,
    CONSTRAINT galeri_approval_status_check CHECK (((approval_status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.galeri OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 61092)
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
-- TOC entry 3562 (class 0 OID 0)
-- Dependencies: 223
-- Name: galeri_id_foto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.galeri_id_foto_seq OWNED BY public.galeri.id_foto;


--
-- TOC entry 224 (class 1259 OID 61093)
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
-- TOC entry 225 (class 1259 OID 61098)
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
-- TOC entry 3563 (class 0 OID 0)
-- Dependencies: 225
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jurnal_id_jurnal_seq OWNED BY public.jurnal.id_jurnal;


--
-- TOC entry 226 (class 1259 OID 61099)
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
-- TOC entry 227 (class 1259 OID 61104)
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
-- TOC entry 3564 (class 0 OID 0)
-- Dependencies: 227
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kerjasama_id_kerjasama_seq OWNED BY public.kerjasama.id_kerjasama;


--
-- TOC entry 228 (class 1259 OID 61105)
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
-- TOC entry 229 (class 1259 OID 61110)
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
-- TOC entry 3565 (class 0 OID 0)
-- Dependencies: 229
-- Name: kontak_id_kontak_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kontak_id_kontak_seq OWNED BY public.kontak.id_kontak;


--
-- TOC entry 230 (class 1259 OID 61111)
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
-- TOC entry 231 (class 1259 OID 61117)
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
-- TOC entry 3566 (class 0 OID 0)
-- Dependencies: 231
-- Name: member_id_member_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.member_id_member_seq OWNED BY public.member.id_member;


--
-- TOC entry 244 (class 1259 OID 61279)
-- Name: pending_changes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pending_changes (
    id_pending integer NOT NULL,
    table_name character varying(50) NOT NULL,
    record_id integer,
    action_type character varying(20) NOT NULL,
    old_data jsonb,
    new_data jsonb,
    user_id integer NOT NULL,
    user_role character varying(20) NOT NULL,
    status character varying(20) DEFAULT 'pending'::character varying,
    reason_rejection text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    reviewed_at timestamp without time zone,
    reviewed_by integer,
    CONSTRAINT pending_changes_action_type_check CHECK (((action_type)::text = ANY ((ARRAY['create'::character varying, 'update'::character varying, 'delete'::character varying])::text[]))),
    CONSTRAINT pending_changes_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.pending_changes OWNER TO postgres;

--
-- TOC entry 3567 (class 0 OID 0)
-- Dependencies: 244
-- Name: TABLE pending_changes; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.pending_changes IS 'Tabel untuk menyimpan semua perubahan yang menunggu approval dari admin';


--
-- TOC entry 3568 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN pending_changes.action_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.pending_changes.action_type IS 'Jenis aksi: create, update, atau delete';


--
-- TOC entry 3569 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN pending_changes.old_data; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.pending_changes.old_data IS 'Data lama sebelum perubahan (untuk update dan delete)';


--
-- TOC entry 3570 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN pending_changes.new_data; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.pending_changes.new_data IS 'Data baru setelah perubahan (untuk create dan update)';


--
-- TOC entry 243 (class 1259 OID 61278)
-- Name: pending_changes_id_pending_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pending_changes_id_pending_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pending_changes_id_pending_seq OWNER TO postgres;

--
-- TOC entry 3571 (class 0 OID 0)
-- Dependencies: 243
-- Name: pending_changes_id_pending_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pending_changes_id_pending_seq OWNED BY public.pending_changes.id_pending;


--
-- TOC entry 232 (class 1259 OID 61118)
-- Name: pengumuman; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengumuman (
    id_pengumuman integer NOT NULL,
    judul character varying(200),
    informasi text,
    id_anggota integer,
    tanggal date,
    status character varying(20),
    approval_status character varying(20) DEFAULT 'approved'::character varying,
    created_by integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    approved_by integer,
    approved_at timestamp without time zone,
    CONSTRAINT pengumuman_approval_status_check CHECK (((approval_status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.pengumuman OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 61123)
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
-- TOC entry 3572 (class 0 OID 0)
-- Dependencies: 233
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengumuman_id_pengumuman_seq OWNED BY public.pengumuman.id_pengumuman;


--
-- TOC entry 234 (class 1259 OID 61124)
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
    id_member integer,
    approval_status character varying(20) DEFAULT 'approved'::character varying,
    created_by integer,
    approved_by integer,
    approved_at timestamp without time zone,
    CONSTRAINT publikasi_approval_status_check CHECK (((approval_status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.publikasi OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 61131)
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
-- TOC entry 3573 (class 0 OID 0)
-- Dependencies: 235
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.publikasi_id_publikasi_seq OWNED BY public.publikasi.id_publikasi;


--
-- TOC entry 236 (class 1259 OID 61132)
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
-- TOC entry 237 (class 1259 OID 61138)
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
-- TOC entry 3574 (class 0 OID 0)
-- Dependencies: 237
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- TOC entry 238 (class 1259 OID 61139)
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
-- TOC entry 239 (class 1259 OID 61147)
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
-- TOC entry 3575 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 240 (class 1259 OID 61148)
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
-- TOC entry 241 (class 1259 OID 61152)
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
-- TOC entry 242 (class 1259 OID 61157)
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
-- TOC entry 3260 (class 2604 OID 61161)
-- Name: agenda id_agenda; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda ALTER COLUMN id_agenda SET DEFAULT nextval('public.agenda_id_agenda_seq'::regclass);


--
-- TOC entry 3261 (class 2604 OID 61162)
-- Name: anggota id_anggota; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota ALTER COLUMN id_anggota SET DEFAULT nextval('public.anggota_id_anggota_seq'::regclass);


--
-- TOC entry 3265 (class 2604 OID 61163)
-- Name: berita id_berita; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita ALTER COLUMN id_berita SET DEFAULT nextval('public.berita_id_berita_seq'::regclass);


--
-- TOC entry 3270 (class 2604 OID 61164)
-- Name: fasilitas id_fasilitas; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas ALTER COLUMN id_fasilitas SET DEFAULT nextval('public.fasilitas_id_fasilitas_seq'::regclass);


--
-- TOC entry 3272 (class 2604 OID 61165)
-- Name: galeri id_foto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri ALTER COLUMN id_foto SET DEFAULT nextval('public.galeri_id_foto_seq'::regclass);


--
-- TOC entry 3276 (class 2604 OID 61166)
-- Name: jurnal id_jurnal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal ALTER COLUMN id_jurnal SET DEFAULT nextval('public.jurnal_id_jurnal_seq'::regclass);


--
-- TOC entry 3277 (class 2604 OID 61167)
-- Name: kerjasama id_kerjasama; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama ALTER COLUMN id_kerjasama SET DEFAULT nextval('public.kerjasama_id_kerjasama_seq'::regclass);


--
-- TOC entry 3278 (class 2604 OID 61168)
-- Name: kontak id_kontak; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak ALTER COLUMN id_kontak SET DEFAULT nextval('public.kontak_id_kontak_seq'::regclass);


--
-- TOC entry 3279 (class 2604 OID 61169)
-- Name: member id_member; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member ALTER COLUMN id_member SET DEFAULT nextval('public.member_id_member_seq'::regclass);


--
-- TOC entry 3293 (class 2604 OID 61282)
-- Name: pending_changes id_pending; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_changes ALTER COLUMN id_pending SET DEFAULT nextval('public.pending_changes_id_pending_seq'::regclass);


--
-- TOC entry 3280 (class 2604 OID 61170)
-- Name: pengumuman id_pengumuman; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman ALTER COLUMN id_pengumuman SET DEFAULT nextval('public.pengumuman_id_pengumuman_seq'::regclass);


--
-- TOC entry 3284 (class 2604 OID 61171)
-- Name: publikasi id_publikasi; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi ALTER COLUMN id_publikasi SET DEFAULT nextval('public.publikasi_id_publikasi_seq'::regclass);


--
-- TOC entry 3288 (class 2604 OID 61172)
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- TOC entry 3290 (class 2604 OID 61173)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3525 (class 0 OID 61061)
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
-- TOC entry 3527 (class 0 OID 61067)
-- Dependencies: 216
-- Data for Name: anggota; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.anggota (id_anggota, nama_gelar, foto, jabatan, email, no_telp, bidang_keahlian, approval_status, created_by, created_at, updated_at, approved_by, approved_at) FROM stdin;
4	UXHDEWR		ITER432	TJEIR34@GMAIL.COM	T83U9423	TE8RJO34	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
3	Citra Dewi, S.T., M.Eng.	../assets/img/anggota/anggota_1765812755_fasilitas_1765669641_a02898ed40d7711990aef44916e905f8.jpg	Asisten Laboratorium	citra.d@lab.id	085098765432	Machine Learning	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
2	Ms. Eve	../assets/img/anggota/anggota_1765813246_be56a5f3e05bf1069ca08df2165b5e07.jpg	Staf Penelitr	budi.s@lab.id	081234567890	Pemrosesan Citra	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
5	EDIT	../assets/img/anggota/anggota_1765815033_135224471_p0.png	EDIT	EDIT@GMAIL.COM	EDIT	EDIT	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
10	Dr. Ir. Ahmad Subarjo, M.T.	ketua.jpg	Ketua	ahmad.subarjo@lab.id	081122334455	Sistem Kontrol & Robotika	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
11	Siti Aminah, S.Kom., M.Cs.	sekretaris.jpg	Sekretaris	siti.aminah@lab.id	081223344556	Kecerdasan Buatan	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
12	Budi Cahyono, S.T.	anggota1.jpg	Anggota	budi.c@lab.id	081334455667	Internet of Things	approved	\N	2025-12-18 15:45:40.940432	2025-12-18 15:45:40.940432	\N	\N
\.


--
-- TOC entry 3529 (class 0 OID 61073)
-- Dependencies: 218
-- Data for Name: berita; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.berita (id_berita, judul, gambar, informasi, tanggal, author, created_at, status, approval_status, created_by, updated_at, approved_by, approved_at) FROM stdin;
45	IEJR3442	../assets/img/berita/be56a5f3e05bf1069ca08df2165b5e07.jpg	daee	2025-12-17	2	2025-12-14 02:45:48.511126	approved	approved	\N	2025-12-18 15:45:40.923597	\N	\N
47	UHRER	../assets/img/berita/1765666755_135224471_p0.png	ijerji-2	2025-12-13	2	2025-12-14 05:59:15.527441	approved	approved	\N	2025-12-18 15:45:40.923597	\N	\N
33	TRY5N	../assets/img/berita/Screenshot_2025-11-22_140415.png	ETERERTERIEREIR	2025-11-18	\N	2025-12-13 17:37:24.832957	rejected	approved	\N	2025-12-18 15:45:40.923597	\N	\N
49	MTYr	../assets/img/berita/a02898ed40d7711990aef44916e905f8.jpg	MTY	2025-12-11	2	2025-12-16 18:01:16.64169	pending	approved	\N	2025-12-18 19:54:00.386365	\N	\N
52	aaaa	../assets/img/berita/Screenshot_2025-12-07_114323.png	wewijew	2025-12-18	4	2025-12-18 17:18:32.538854	pending	approved	\N	2025-12-18 20:40:52.296986	4	2025-12-18 20:40:52.296986
\.


--
-- TOC entry 3531 (class 0 OID 61081)
-- Dependencies: 220
-- Data for Name: fasilitas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fasilitas (id_fasilitas, nama_fasilitas, deskripsi, foto, created_by, approval_status, approved_by, approved_at) FROM stdin;
3	Printer 3D FDM Skala Besar SDD	Digunakan untuk prototyping cepat komponenSSD robotika dan mekanik.	../assets/img/fasilitas/fasilitas_1765669641_a02898ed40d7711990aef44916e905f8.jpg	3	approved	\N	\N
2	Server GPU Nvidia Tesla V100	Server berkapasitas tinggi untuk komputasi deep learning dan AI.	../assets/img/fasilitas/fasilitas_1765669690_f2c75990a45f197d9f18c2c099f8751e.jpg	2	approved	\N	\N
1	Robot Lengan Industri (5-DOF)	Robot lengan 5 derajat kebebasan untuk eksperimen kontrol gerak dan *pick-and-place*.	../assets/img/fasilitas/fasilitas_1765669702_135224471_p0.png	1	approved	\N	\N
4	i23	RRIU32	../assets/img/fasilitas/fasilitas_1765670046_Screenshot_2025-11-21_011745.png	1	approved	\N	\N
\.


--
-- TOC entry 3533 (class 0 OID 61087)
-- Dependencies: 222
-- Data for Name: galeri; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.galeri (id_foto, nama_foto, deskripsi, file_foto, id_anggota, updated_by, status, approval_status, created_at, updated_at, created_by, approved_by, approved_at) FROM stdin;
10	HJRU	4545	../assets/img/galeri/galeri_1765794301_a02898ed40d7711990aef44916e905f8.jpg	3	1	approved	approved	2025-12-18 15:45:40.930423	2025-12-18 15:45:40.930423	\N	\N	\N
5	IGaleri5	IGaleri5	../assets/img/galeri/galeri_1765723851_Screenshot_2025-11-22_183937.png	4	1	pending	approved	2025-12-18 15:45:40.930423	2025-12-18 15:45:40.930423	\N	\N	\N
3	iGaleri3	ietto	../assets/img/galeri/galeri_1765673943_Screenshot_2025-11-20_170828.png	2	2	approved	approved	2025-12-18 15:45:40.930423	2025-12-18 15:45:40.930423	\N	\N	\N
8	HJRUJH$#	423rr3wr	../assets/img/galeri/galeri_1765730619_f2c75990a45f197d9f18c2c099f8751e.jpg	4	1	approved	approved	2025-12-18 15:45:40.930423	2025-12-18 15:45:40.930423	\N	\N	\N
7	IGaleri6	RW	../assets/img/galeri/galeri_1765729721_135224471_p0.png	4	1	approved	approved	2025-12-18 15:45:40.930423	2025-12-18 15:45:40.930423	\N	\N	\N
\.


--
-- TOC entry 3535 (class 0 OID 61093)
-- Dependencies: 224
-- Data for Name: jurnal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jurnal (id_jurnal, judul, tanggal_upload, penyusun, link_jurnal) FROM stdin;
\.


--
-- TOC entry 3537 (class 0 OID 61099)
-- Dependencies: 226
-- Data for Name: kerjasama; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kerjasama (id_kerjasama, nama_perusahaan, proposal, contact_perusahaan, id_anggota) FROM stdin;
\.


--
-- TOC entry 3539 (class 0 OID 61105)
-- Dependencies: 228
-- Data for Name: kontak; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kontak (id_kontak, nama, email, no_telp, deskripsi_tujuan, opsi_kerjasama, id_anggota) FROM stdin;
\.


--
-- TOC entry 3541 (class 0 OID 61111)
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
-- TOC entry 3552 (class 0 OID 61279)
-- Dependencies: 244
-- Data for Name: pending_changes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pending_changes (id_pending, table_name, record_id, action_type, old_data, new_data, user_id, user_role, status, reason_rejection, created_at, reviewed_at, reviewed_by) FROM stdin;
\.


--
-- TOC entry 3543 (class 0 OID 61118)
-- Dependencies: 232
-- Data for Name: pengumuman; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pengumuman (id_pengumuman, judul, informasi, id_anggota, tanggal, status, approval_status, created_by, created_at, updated_at, approved_by, approved_at) FROM stdin;
1	Pendaftaran Asisten Lab Semester Genap 2026	Pendaftaran dibuka mulai 10 hingga 20 Desember 2025. Silakan kirim CV dan transkrip nilai ke email lab.	\N	2025-01-02	pending	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
2	Jadwal Review Proyek Akhir	Review proyek akhir untuk batch 2023 akan dilaksanakan pada minggu ketiga Januari 2026. Detail jadwal akan diumumkan melalui email.	2	2025-01-03	pending	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
5	judul5	info judul5	\N	2025-01-01	pending	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
3	Penggunaan Ruangan Komputasi	Ruangan komputasi akan ditutup sementara pada tanggal 5 Desember 2025 untuk pemeliharaan sistem. Harap simpan pekerjaan Anda sebelum tanggal tersebut.	3	2025-01-05	approved	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
4	judul4	info judul4	2	2025-01-08	approved	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
9	EDIT2	EDIT	5	2025-12-12	pending	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
10	2323232	34343434	2	2025-12-16	approved	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
11	PENDING	PENDING	5	2025-12-12	approved	approved	\N	2025-12-18 15:45:40.933632	2025-12-18 15:45:40.933632	\N	\N
\.


--
-- TOC entry 3545 (class 0 OID 61124)
-- Dependencies: 234
-- Data for Name: publikasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.publikasi (id_publikasi, judul, penulis, tanggal_terbit, file_publikasi, deskripsi, created_at, updated_at, id_anggota, status, id_member, approval_status, created_by, approved_by, approved_at) FROM stdin;
4	5JUDUL4	IEJRIER5	2025-12-02	../assets/files/publikasi/publikasi_1765793435_English_Assignment_EX10.pdf	EIJRIERrwr	2025-12-14 02:53:44.249846	2025-12-14 02:53:44.249846	\N	pending	1	approved	\N	\N	\N
3	Review Metode Sensor Fusion3	Sinta Amelia, Citra Dewi	2025-11-20	file_publikasi_12.pdf	Makalah review tentang berbagai teknik fusi data sensor.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	3	rejected	\N	approved	\N	\N	\N
2	Analisis Performa Sistem Kontrol PIDe	Doni Saputra, Budi Santoso	2024-05-01	file_publikasi_11.pdf	Skripsi alumni tentang optimasi kontrol PID pada lengan robot.	2025-12-04 00:41:54.5758	2025-12-04 00:41:54.5758	2	pending	\N	approved	\N	\N	\N
12	iu394x	33i4j34	2025-12-16	../assets/files/publikasi/publikasi_1765855692_English_Assignment_EX10.pdf	2323	2025-12-16 10:28:12.128965	2025-12-16 10:28:12.128965	2	pending	\N	approved	\N	\N	\N
14	CRUD	CRUD	2025-12-12	../assets/files/publikasi/publikasi_1765883484_ENGLISH_U6.pdf	CRUD	2025-12-16 18:11:24.080816	2025-12-16 18:11:24.080816	3	approved	\N	approved	\N	\N	\N
20	EDT	EDT	2025-12-12	../assets/files/publikasi/publikasi_1765902769_publikasi_1765643036_BimaAdiwijaya_Polimorfisme.pdf	EDT	2025-12-16 23:32:30.86939	2025-12-16 23:32:30.86939	5	pending	\N	approved	\N	\N	\N
9	rwwewe	ewewewee	2025-12-05	../assets/files/publikasi/publikasi_1765793546_ENGLISH_U6.pdf	ewew4wewe	2025-12-15 17:12:07.920172	2025-12-15 17:12:07.920172	\N	approved	1	approved	\N	\N	\N
5	TRY5N	IEJRIER5	2025-12-10	../assets/files/publikasi/publikasi_1765785106_ENGLISH_U6.pdf	e42	2025-12-15 14:45:18.954999	2025-12-15 14:45:18.954999	\N	approved	1	approved	\N	\N	\N
\.


--
-- TOC entry 3547 (class 0 OID 61132)
-- Dependencies: 236
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.settings (id, key, value, updated_at, updated_by) FROM stdin;
2	logo	../assets/img/logo_1766064767_retestinger.png	2025-12-18 20:32:47.277424	4
3	email	data_technologies@polinema.ac.id	2025-12-18 20:41:21.833645	4
4	no_telepon	(00000) 0123456	2025-12-18 20:42:41.389109	4
5	medsos_linkedin	https://www.linkedin.com/	2025-12-18 20:43:58.345085	4
6	medsos_youtube	https://www.youtube.com/	2025-12-18 20:44:33.140747	4
\.


--
-- TOC entry 3549 (class 0 OID 61139)
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
-- TOC entry 3576 (class 0 OID 0)
-- Dependencies: 215
-- Name: agenda_id_agenda_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.agenda_id_agenda_seq', 13, true);


--
-- TOC entry 3577 (class 0 OID 0)
-- Dependencies: 217
-- Name: anggota_id_anggota_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.anggota_id_anggota_seq', 12, true);


--
-- TOC entry 3578 (class 0 OID 0)
-- Dependencies: 219
-- Name: berita_id_berita_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.berita_id_berita_seq', 52, true);


--
-- TOC entry 3579 (class 0 OID 0)
-- Dependencies: 221
-- Name: fasilitas_id_fasilitas_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fasilitas_id_fasilitas_seq', 6, true);


--
-- TOC entry 3580 (class 0 OID 0)
-- Dependencies: 223
-- Name: galeri_id_foto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.galeri_id_foto_seq', 11, true);


--
-- TOC entry 3581 (class 0 OID 0)
-- Dependencies: 225
-- Name: jurnal_id_jurnal_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jurnal_id_jurnal_seq', 1, false);


--
-- TOC entry 3582 (class 0 OID 0)
-- Dependencies: 227
-- Name: kerjasama_id_kerjasama_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kerjasama_id_kerjasama_seq', 1, false);


--
-- TOC entry 3583 (class 0 OID 0)
-- Dependencies: 229
-- Name: kontak_id_kontak_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kontak_id_kontak_seq', 1, false);


--
-- TOC entry 3584 (class 0 OID 0)
-- Dependencies: 231
-- Name: member_id_member_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.member_id_member_seq', 12, true);


--
-- TOC entry 3585 (class 0 OID 0)
-- Dependencies: 243
-- Name: pending_changes_id_pending_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pending_changes_id_pending_seq', 1, false);


--
-- TOC entry 3586 (class 0 OID 0)
-- Dependencies: 233
-- Name: pengumuman_id_pengumuman_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pengumuman_id_pengumuman_seq', 12, true);


--
-- TOC entry 3587 (class 0 OID 0)
-- Dependencies: 235
-- Name: publikasi_id_publikasi_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.publikasi_id_publikasi_seq', 22, true);


--
-- TOC entry 3588 (class 0 OID 0)
-- Dependencies: 237
-- Name: settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.settings_id_seq', 2, true);


--
-- TOC entry 3589 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- TOC entry 3307 (class 2606 OID 61175)
-- Name: agenda agenda_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (id_agenda);


--
-- TOC entry 3309 (class 2606 OID 61177)
-- Name: anggota anggota_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota
    ADD CONSTRAINT anggota_pkey PRIMARY KEY (id_anggota);


--
-- TOC entry 3311 (class 2606 OID 61179)
-- Name: berita berita_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT berita_pkey PRIMARY KEY (id_berita);


--
-- TOC entry 3314 (class 2606 OID 61181)
-- Name: fasilitas fasilitas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fasilitas_pkey PRIMARY KEY (id_fasilitas);


--
-- TOC entry 3316 (class 2606 OID 61183)
-- Name: galeri galeri_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT galeri_pkey PRIMARY KEY (id_foto);


--
-- TOC entry 3319 (class 2606 OID 61185)
-- Name: jurnal jurnal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT jurnal_pkey PRIMARY KEY (id_jurnal);


--
-- TOC entry 3321 (class 2606 OID 61187)
-- Name: kerjasama kerjasama_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama
    ADD CONSTRAINT kerjasama_pkey PRIMARY KEY (id_kerjasama);


--
-- TOC entry 3323 (class 2606 OID 61189)
-- Name: kontak kontak_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT kontak_pkey PRIMARY KEY (id_kontak);


--
-- TOC entry 3325 (class 2606 OID 61191)
-- Name: member member_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member
    ADD CONSTRAINT member_email_key UNIQUE (email);


--
-- TOC entry 3327 (class 2606 OID 61193)
-- Name: member member_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member
    ADD CONSTRAINT member_pkey PRIMARY KEY (id_member);


--
-- TOC entry 3347 (class 2606 OID 61290)
-- Name: pending_changes pending_changes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_changes
    ADD CONSTRAINT pending_changes_pkey PRIMARY KEY (id_pending);


--
-- TOC entry 3330 (class 2606 OID 61195)
-- Name: pengumuman pengumuman_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT pengumuman_pkey PRIMARY KEY (id_pengumuman);


--
-- TOC entry 3333 (class 2606 OID 61197)
-- Name: publikasi publikasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT publikasi_pkey PRIMARY KEY (id_publikasi);


--
-- TOC entry 3335 (class 2606 OID 61199)
-- Name: settings settings_key_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_key UNIQUE (key);


--
-- TOC entry 3337 (class 2606 OID 61201)
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- TOC entry 3339 (class 2606 OID 61203)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3341 (class 2606 OID 61205)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3343 (class 2606 OID 61207)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 3312 (class 1259 OID 61377)
-- Name: idx_berita_approval; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_berita_approval ON public.berita USING btree (approval_status);


--
-- TOC entry 3317 (class 1259 OID 61379)
-- Name: idx_galeri_approval; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_galeri_approval ON public.galeri USING btree (approval_status);


--
-- TOC entry 3344 (class 1259 OID 61375)
-- Name: idx_pending_changes_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pending_changes_status ON public.pending_changes USING btree (status);


--
-- TOC entry 3345 (class 1259 OID 61376)
-- Name: idx_pending_changes_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pending_changes_user ON public.pending_changes USING btree (user_id);


--
-- TOC entry 3328 (class 1259 OID 61380)
-- Name: idx_pengumuman_approval; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengumuman_approval ON public.pengumuman USING btree (approval_status);


--
-- TOC entry 3331 (class 1259 OID 61378)
-- Name: idx_publikasi_approval; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_publikasi_approval ON public.publikasi USING btree (approval_status);


--
-- TOC entry 3375 (class 2620 OID 61386)
-- Name: anggota update_anggota_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_anggota_updated_at BEFORE UPDATE ON public.anggota FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3376 (class 2620 OID 61382)
-- Name: berita update_berita_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_berita_updated_at BEFORE UPDATE ON public.berita FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3377 (class 2620 OID 61384)
-- Name: galeri update_galeri_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_galeri_updated_at BEFORE UPDATE ON public.galeri FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3378 (class 2620 OID 61385)
-- Name: pengumuman update_pengumuman_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_pengumuman_updated_at BEFORE UPDATE ON public.pengumuman FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3379 (class 2620 OID 61383)
-- Name: publikasi update_publikasi_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_publikasi_updated_at BEFORE UPDATE ON public.publikasi FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3351 (class 2606 OID 61208)
-- Name: berita berita_author_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT berita_author_fkey FOREIGN KEY (author) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3348 (class 2606 OID 61213)
-- Name: agenda fk_agenda_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agenda
    ADD CONSTRAINT fk_agenda_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3349 (class 2606 OID 61370)
-- Name: anggota fk_anggota_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota
    ADD CONSTRAINT fk_anggota_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3350 (class 2606 OID 61365)
-- Name: anggota fk_anggota_created_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anggota
    ADD CONSTRAINT fk_anggota_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3352 (class 2606 OID 61325)
-- Name: berita fk_berita_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT fk_berita_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3353 (class 2606 OID 61218)
-- Name: berita fk_berita_author; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT fk_berita_author FOREIGN KEY (author) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3354 (class 2606 OID 61320)
-- Name: berita fk_berita_created_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.berita
    ADD CONSTRAINT fk_berita_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3355 (class 2606 OID 61360)
-- Name: fasilitas fk_fasilitas_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fk_fasilitas_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3356 (class 2606 OID 61223)
-- Name: fasilitas fk_fasilitas_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fasilitas
    ADD CONSTRAINT fk_fasilitas_user FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3357 (class 2606 OID 61228)
-- Name: galeri fk_galeri_admin; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_admin FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3358 (class 2606 OID 61233)
-- Name: galeri fk_galeri_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3359 (class 2606 OID 61345)
-- Name: galeri fk_galeri_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3360 (class 2606 OID 61340)
-- Name: galeri fk_galeri_created_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri
    ADD CONSTRAINT fk_galeri_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3361 (class 2606 OID 61238)
-- Name: jurnal fk_jurnal_penyusun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT fk_jurnal_penyusun FOREIGN KEY (penyusun) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3363 (class 2606 OID 61243)
-- Name: kerjasama fk_kerjasama_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kerjasama
    ADD CONSTRAINT fk_kerjasama_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3364 (class 2606 OID 61248)
-- Name: kontak fk_kontak_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT fk_kontak_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3365 (class 2606 OID 61253)
-- Name: pengumuman fk_pengumuman_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT fk_pengumuman_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3366 (class 2606 OID 61355)
-- Name: pengumuman fk_pengumuman_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT fk_pengumuman_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3367 (class 2606 OID 61350)
-- Name: pengumuman fk_pengumuman_created_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengumuman
    ADD CONSTRAINT fk_pengumuman_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3368 (class 2606 OID 61258)
-- Name: publikasi fk_publikasi_anggota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_anggota FOREIGN KEY (id_anggota) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3369 (class 2606 OID 61335)
-- Name: publikasi fk_publikasi_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_approved_by FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3370 (class 2606 OID 61330)
-- Name: publikasi fk_publikasi_created_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3371 (class 2606 OID 61263)
-- Name: publikasi fk_publikasi_member; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publikasi
    ADD CONSTRAINT fk_publikasi_member FOREIGN KEY (id_member) REFERENCES public.member(id_member) ON DELETE SET NULL;


--
-- TOC entry 3372 (class 2606 OID 61268)
-- Name: settings fk_settings_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT fk_settings_user FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3362 (class 2606 OID 61273)
-- Name: jurnal jurnal_penyusun_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurnal
    ADD CONSTRAINT jurnal_penyusun_fkey FOREIGN KEY (penyusun) REFERENCES public.anggota(id_anggota) ON DELETE SET NULL;


--
-- TOC entry 3373 (class 2606 OID 61296)
-- Name: pending_changes pending_changes_reviewed_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_changes
    ADD CONSTRAINT pending_changes_reviewed_by_fkey FOREIGN KEY (reviewed_by) REFERENCES public.users(id);


--
-- TOC entry 3374 (class 2606 OID 61291)
-- Name: pending_changes pending_changes_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_changes
    ADD CONSTRAINT pending_changes_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


-- Completed on 2025-12-18 20:46:15

--
-- PostgreSQL database dump complete
--

\unrestrict dUiHOFXgM1J7soEe1HDgYU4WIuPnxYG2Z8v1t15qTYwucLGJBrn1OcX7GHvo24w

