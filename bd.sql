CREATE DATABASE IF NOT EXISTS udev_db;
USE udev_db;

-- Tabla instituciones
CREATE TABLE IF NOT EXISTS instituciones (
  id_institucion BIGINT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(25) NOT NULL,
  direccion VARCHAR(100) NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_institucion)
);

-- Tabla salones
CREATE TABLE IF NOT EXISTS salones (
  id_salon INT NOT NULL AUTO_INCREMENT,
  nombre_salon VARCHAR(25) NOT NULL,
  capacidad INT NOT NULL,
  descripcion VARCHAR(100) NOT NULL,
  id_institucion BIGINT NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_salon),
  CONSTRAINT fk_salones_institucion FOREIGN KEY (id_institucion) REFERENCES instituciones (id_institucion)
);

-- Tabla periodos
CREATE TABLE IF NOT EXISTS periodos (
  id_periodo INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_periodo)
);

-- Tabla docentes
CREATE TABLE IF NOT EXISTS docentes (
  numero_documento VARCHAR(20) NOT NULL,
  tipo_documento VARCHAR(20) NOT NULL,
  nombres VARCHAR(25) NOT NULL,
  apellidos VARCHAR(25) NOT NULL,
  telefono VARCHAR(25) NOT NULL,
  direccion VARCHAR(25) NOT NULL,
  perfil_profesional VARCHAR(255) NOT NULL,
  declara_renta TINYINT NOT NULL,
  retenedor_iva TINYINT NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (numero_documento)
);

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  correo VARCHAR(70) DEFAULT NULL,
  clave VARCHAR(255) DEFAULT NULL,	
  rol VARCHAR(25) DEFAULT NULL,
  numero_documento VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (numero_documento) REFERENCES docentes(numero_documento) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabla cuentas_cobro
CREATE TABLE IF NOT EXISTS cuentas_cobro (
  id_cuenta INT NOT NULL AUTO_INCREMENT,
  fecha DATE NOT NULL,
  valor_hora INT NOT NULL,
  horas_trabajadas INT NOT NULL,
  numero_documento VARCHAR(20) NOT NULL,
  estado ENUM('creada', 'aceptada_docente', 'pendiente_firma', 'proceso_pago', 'pagada', 'rechazada_por_docente') NOT NULL DEFAULT 'creada',
  PRIMARY KEY (id_cuenta),
  CONSTRAINT fk_cuentas_cobro_docente FOREIGN KEY (numero_documento) REFERENCES docentes (numero_documento)
);

CREATE TABLE IF NOT EXISTS abonos (
  id_abono INT NOT NULL AUTO_INCREMENT,
  id_cuenta INT NOT NULL,
  fecha_abono DATE NOT NULL,
  valor_abonado INT NOT NULL,
  PRIMARY KEY (id_abono),
  CONSTRAINT fk_abonos_cuenta FOREIGN KEY (id_cuenta) REFERENCES cuentas_cobro(id_cuenta)
);

-- Tabla programas
CREATE TABLE IF NOT EXISTS programas (
  id_programa INT NOT NULL AUTO_INCREMENT,
  tipo VARCHAR(25) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  duracion_meses INT NOT NULL,
  valor_total_programa INT DEFAULT NULL,
  descripcion VARCHAR(100) NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_programa)
);

-- Tabla modulos
CREATE TABLE IF NOT EXISTS modulos (
  id_modulo INT NOT NULL AUTO_INCREMENT,
  id_programa INT NOT NULL,
  tipo VARCHAR(100) NOT NULL,
  nombre VARCHAR(25) NOT NULL,
  descripcion VARCHAR(100) NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_modulo),
  CONSTRAINT fk_modulos_programa FOREIGN KEY (id_programa) REFERENCES programas (id_programa)
);

-- Tabla docente_modulo (tabla intermedia)
CREATE TABLE IF NOT EXISTS docente_modulo (
  id_docente_modulo INT NOT NULL AUTO_INCREMENT,
  numero_documento VARCHAR(20) NOT NULL,
  id_modulo INT NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (id_docente_modulo),
  CONSTRAINT fk_docente_modulo_docente FOREIGN KEY (numero_documento) 
    REFERENCES docentes (numero_documento) ON DELETE CASCADE,
  CONSTRAINT fk_docente_modulo_modulo FOREIGN KEY (id_modulo) 
    REFERENCES modulos (id_modulo) ON DELETE CASCADE,
  UNIQUE KEY (numero_documento, id_modulo) -- Evita duplicados
);

-- Tabla programador
CREATE TABLE IF NOT EXISTS programador (
  id_programador INT NOT NULL AUTO_INCREMENT,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_salida TIME NOT NULL,
  id_salon INT NOT NULL,
  numero_documento VARCHAR(20) NOT NULL,
  id_modulo INT NOT NULL,
  id_periodo INT NOT NULL,
  modalidad VARCHAR(20) NOT NULL,
  estado ENUM('Perdida', 'Vista', 'Pendiente', 'Reprogramada') NOT NULL DEFAULT 'Pendiente',
  clase_original_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_programador),
  CONSTRAINT fk_asignacion_periodo FOREIGN KEY (id_periodo) REFERENCES periodos (id_periodo),
  CONSTRAINT fk_programador_salon FOREIGN KEY (id_salon) REFERENCES salones (id_salon),
  CONSTRAINT fk_programador_docente FOREIGN KEY (numero_documento) REFERENCES docentes (numero_documento),
  CONSTRAINT fk_programador_modulo FOREIGN KEY (id_modulo) REFERENCES modulos (id_modulo),
  CONSTRAINT fk_programador_original FOREIGN KEY (clase_original_id) REFERENCES programador(id_programador)
);

-- Tabla asistencias
CREATE TABLE IF NOT EXISTS asistencias (
  id_asistencia INT NOT NULL AUTO_INCREMENT,
  fecha DATE NOT NULL,
  hora_entrada TIME DEFAULT NULL,
  hora_salida TIME DEFAULT NULL,
  id_programador INT NOT NULL,
  estado ENUM('cumplida', 'perdida') NOT NULL DEFAULT 'cumplida',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_asistencia),
  CONSTRAINT fk_asistencias_programador FOREIGN KEY (id_programador) REFERENCES programador(id_programador)
);	

-- Tabla estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
  codigo_estudiante INT NOT NULL AUTO_INCREMENT,
  nombre_estudiante VARCHAR(50) NOT NULL,
  apellidos_estudiante VARCHAR(50) NOT NULL,
  fecha_nacimiento_estudiante VARCHAR(20) DEFAULT NULL,
  imagen VARCHAR(250) DEFAULT NULL,
  estado VARCHAR(10) DEFAULT NULL,
  PRIMARY KEY (codigo_estudiante)
);

-- Tabla movimientos
CREATE TABLE IF NOT EXISTS movimientos (
  id_movimiento INT NOT NULL AUTO_INCREMENT,
  descripcion VARCHAR(255) NOT NULL,
  fecha_movimiento DATE NOT NULL,
  id_programa INT NOT NULL,
  PRIMARY KEY (id_movimiento),
  CONSTRAINT movimientos_ibfk_1 FOREIGN KEY (id_programa) REFERENCES programas (id_programa)
);

-- Tabla tipo_convenio
CREATE TABLE IF NOT EXISTS tipo_convenio (
  codigo_tipo_convenio INT NOT NULL,
  descripcion_tipo_usuario VARCHAR(60) DEFAULT NULL,
  valor_descuento FLOAT DEFAULT NULL,
  estado TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (codigo_tipo_convenio)
);

-- Tabla convenio
CREATE TABLE IF NOT EXISTS convenio (
  codigo_convenio INT NOT NULL,
  descripcion_convenio VARCHAR(50) NOT NULL,
  valor_total_convenio INT DEFAULT NULL,
  saldo_convenio INT DEFAULT NULL,
  id_programa INT DEFAULT NULL,
  codigo_estudiante INT DEFAULT NULL,
  estado VARCHAR(50) NOT NULL,
  tipo_fk_convenio INT DEFAULT NULL,
  PRIMARY KEY (codigo_convenio),
  INDEX fk_estudiantes_idx (codigo_estudiante ASC),
  INDEX fk_programa_idx (id_programa ASC),
  INDEX fk_tipo_convenio_idx (tipo_fk_convenio ASC),
  CONSTRAINT fk_estudiantes FOREIGN KEY (codigo_estudiante) REFERENCES estudiantes (codigo_estudiante),
  CONSTRAINT fk_programa FOREIGN KEY (id_programa) REFERENCES programas (id_programa),
  CONSTRAINT fk_tipo_convenio FOREIGN KEY (tipo_fk_convenio) REFERENCES tipo_convenio (codigo_tipo_convenio)
);


