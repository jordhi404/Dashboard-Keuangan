WITH Dashboard_CTE 
(
RegistrationNo,
ServiceUnitName,
BedCode,
MedicalNo,
PatientName,
CustomerType,
ChargeClassName,
RencanaPulang,
CatRencanaPulang,
Keperawatan,
TungguJangdik,
TungguFarmasi,
RegistrationStatus,
OutStanding,
SelesaiBilling,
GCPlanDischargeNotesType,
Keterangan,
NoteText
)
AS
(
	  SELECT DISTINCT 
		a.RegistrationNo,
		r.ServiceUnitName,
		a.BedCode,
		a.MedicalNo,
		a.PatientName,
		r.CustomerType,
		r.ChargeClassName,
		RencanaPulang = 
			CASE 
				WHEN cv.PlanDischargeTime IS NULL
					THEN CAST(cv.PlanDischargeDate AS VARCHAR) + ' ' + CAST(cv.PlanDischargeTime AS VARCHAR)
				ELSE CAST(cv.PlanDischargeDate AS DATETIME) + CAST(cv.PlanDischargeTime AS TIME)
			END,
		CatRencanaPulang = cv.PlanDischargeNotes,
		Keperawatan =
		(SELECT TOP 1 TransactionNo 
		FROM PatientChargesHD
			WHERE VisitID=cv.VisitID 
			and GCTransactionStatus<>'X121^999' 
			and GCTransactionStatus in ('X121^001','X121^002','X121^003')
			and HealthcareServiceUnitID not in (82,83,99,138,140,101,137)
			ORDER BY TestOrderID ASC),
		TungguJangdik = -- Pilih yang masih statusnya open, received and in progress
		(SELECT TOP 1 TransactionNo 
		FROM PatientChargesHD
			WHERE VisitID=cv.VisitID 
				and GCTransactionStatus<>'X121^999' 
				and GCTransactionStatus in ('X121^001','X121^002','X121^003')
				and HealthcareServiceUnitID in (82,83,99,138,140)
			ORDER BY TestOrderID ASC),
		TungguFarmasi = -- Pilih yang masih statusnya open, received and in progress
		(SELECT TOP 1 TransactionNo 
		FROM PatientChargesHD
			WHERE VisitID=cv.VisitID 
			and GCTransactionStatus<>'X121^999' 
			and GCTransactionStatus in ('X121^001','X121^002','X121^003')
			and HealthcareServiceUnitID in (101,137)
			ORDER BY TestOrderID ASC),
		RegistrationStatus = (SELECT TOP 1 IsLockDownNEW
						from   RegistrationStatusLog 
						where  RegistrationID = a.RegistrationID 
						ORDER by ID DESC),
		OutStanding =(SELECT DISTINCT COUNT(GCTransactionStatus) 
			FROM PatientChargesHD 
			where VisitID=cv.VisitID 
				and GCTransactionStatus in ('X121^001','X121^002','X121^003')),
		SelesaiBilling = (SELECT TOP 1 PrintedDate 
			FROM ReportPrintLog 
			WHERE ReportID=7012 
				and ReportParameter = CONCAT('RegistrationID = ',r.RegistrationID) 
			ORDER BY PrintedDate DESC),
		cv.GCPlanDischargeNotesType,
		Keterangan=
		CASE 
			WHEN sc.StandardCodeName = '' or sc.StandardCodeName IS NULL
				THEN  ''
			ELSE sc.StandardCodeName
		END,
		pvn.NoteText
	FROM vBed a
	LEFT JOIN vPatient			p	ON p.MRN=a.MRN
	LEFT JOIN PatientNotes		pn	ON pn.MRN=a.MRN
	LEFT JOIN vRegistration		r	ON r.RegistrationID=a.RegistrationID
	LEFT JOIN ConsultVisit		cv	ON cv.VisitID=r.VisitID
	LEFT JOIN StandardCode		sc	ON sc.StandardCodeID=cv.GCPlanDischargeNotesType
	LEFT JOIN PatientVisitNote	pvn	ON pvn.VisitID=cv.VisitID and pvn.GCNoteType  in ('X312^001','X312^002','X312^003','X312^004','X312^005','X312^006')
	WHERE a.IsDeleted=0 and a.RegistrationID IS NOT NULL
	AND cv.PlanDischargeDate  IS NOT NULL
	AND r.GCRegistrationStatus<>'X020^006' -- Pendaftaran Tidak DiBatalkan
)
SELECT RegistrationNo,
ServiceUnitName,
BedCode,
MedicalNo,
PatientName,
CustomerType,
ChargeClassName,
RencanaPulang,
CatRencanaPulang,
GCPlanDischargeNotesType,
Keterangan=
CASE
            -- Check for Keperawatan
            WHEN Keperawatan is not NULL
                THEN 'TungguKeperawatan'
            -- Check for Jangdik
            WHEN TungguJangdik is not NULL
                THEN 'TungguJangdik'
            -- Check for Farmasi
            WHEN TungguFarmasi is not NULL
                THEN 'TungguFarmasi'
            -- Check for dikerjakan kasir (blm dikunci, ada outstanding dan belum dicetak izin pulang)
            WHEN RegistrationStatus=0 and OutStanding>0 and SelesaiBilling is NULL
                THEN 'TungguKasir'
			 -- Check for dikerjakan kasir (sudah dikunci, tidak ada outstanding dan belum dicetak izin pulang)
            WHEN RegistrationStatus=1 and OutStanding=0 and SelesaiBilling is NULL
                THEN 'TungguKasir'
			-- Check for selesai kasir (sudah dikunci, tanpa outstanding dan sudah dicetak izin pulang)
			WHEN RegistrationStatus=1 and OutStanding=0 and SelesaiBilling is Not NULL
                THEN 'SelesaiKasir'
            END
FROM Dashboard_CTE;