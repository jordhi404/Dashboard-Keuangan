SELECT TOP (100) 
    [BedID],
    [BedCode],
    [RoomID],
    [RoomCode],
    [RoomName],
    [GCBedStatus],
    [BedStatus],
    [ServiceUnitCode],
    [ServiceUnitName],
    [ClassID],
    [ClassCode],
    [ClassName],
    [GCClassRL],
    [LastUnoccupiedDate]
  FROM [MS_RSDOSOBA].[dbo].[vBed]
  WHERE [IsDeleted]=0 
  AND GCBedStatus IN ('0116^H')
  AND ServiceUnitCode IS NOT NULL
  order by [ServiceUnitCode],[LastUnoccupiedDate],[BedCode]
