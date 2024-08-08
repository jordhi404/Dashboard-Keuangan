SELECT DISTINCT 
	a.RegistrationNo,
	r.RegistrationID,
	r.GCRegistrationStatus,
	r.RegistrationStatus,
	r.ServiceUnitCode,
	r.ServiceUnitName,
	a.MedicalNo,
	a.PatientName,
	--p.HomeAddress,
	--a.BusinessPartnerName,
	r.CustomerType,
	r.ChargeClassName,
	a.BedCode,
	a.BedStatus,
	--a.ParamedicCode,
	a.ParamedicName,
	--pn.Notes,
	--cv.PlanDischargeDate,
	--cv.PlanDischargeTime,
	--cv.GCPlanDischargeNotesType,
	RencanaPulang = 
	CASE 
		WHEN cv.PlanDischargeTime IS NULL
		THEN  CAST(cv.PlanDischargeDate as VARCHAR) + CAST(cv.PlanDischargeTime AS VARCHAR)
		ELSE CAST(cv.PlanDischargeDate as DATETIME) + CAST(cv.PlanDischargeTime AS TIME)
    END,
	SelesaiBilling = (SELECT TOP 1 PrintedDate FROM ReportPrintLog 
		WHERE ReportID=7012 and ReportParameter = CONCAT('RegistrationID = ',r.RegistrationID) 
		ORDER BY PrintedDate DESC),
	cv.GCPlanDischargeNotesType,
	Keterangan=
	CASE 
		WHEN sc.StandardCodeName = '' or sc.StandardCodeName IS NULL
		THEN  ''
		ELSE sc.StandardCodeName
    END,
	pvn.GCNoteType,
	pvn.NoteText,
    --cv.PlanDischargeNotes,
	--a.BedID,
	a.RegistrationID
	--a.RoomID,
	--a.RoomCode,
	--a.RoomName,
	--a.GCBedStatus,
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