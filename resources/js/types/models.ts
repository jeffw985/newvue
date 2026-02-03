/**
 * Shared type definitions for application models
 */

export interface Customer {
    id: number
    full_name: string | null
    first: string | null
    last: string | null
    company: string | null
    street: string | null
    city: string | null
    state: string | null
    zipcode: string | null
    phone: string | null
    phone2: string | null
    email: string | null
    email2: string | null
    spouse_first: string | null
    spouse_last: string | null
    irrigation: boolean
    maintenance: boolean
    notes: string | null
    area_group_id: number | null
    xero_contact_id: string | null
    ledgers?: Ledger[]
    irrigations?: Irrigation[]
    maintenances?: Maintenance[]
    service_schedules?: ServiceSchedule[]
    ledgers_count?: number
    irrigations_count?: number
    maintenances_count?: number
    service_schedules_count?: number
}

export interface Prospect {
    id: number
    city: string | null
    confirmation: string | null
    date: string | null
    email: string | null
    name: string | null
    phone: string | null
    state: string | null
    street: string | null
    work_description: string | null
    zip: string | null
}

export interface Irrigation {
    id: number
    cust_id: number
    backflow_brand: string | null
    backflow_location: string | null
    backflow_model: string | null
    backflow_serial: string | null
    backflow_test_date: string | null
    backflow_testing: boolean
    backflow_test_pass: boolean
    backflow_type: string | null
    blowout: boolean
    blowout_date: string | null
    controller_location: string | null
    irrig_notes: string | null
    pvb_ai: string | null
    pvb_ai_opened: boolean
    pvb_cv: string | null
    pvb_cv_held: boolean
    rp_cv1: string | null
    rp_cv1_held: boolean
    rp_cv2: string | null
    rp_cv2_held: boolean
    rp_rv: string | null
    rp_rv_opened: boolean
    site_id: string | null
    turn_on: boolean
    turn_on_date: string | null
    site_address: string | null
    sequence_order: number | null
    subdivision: string | null
    customer?: Customer
}

export interface Maintenance {
    id: number
    cust_id: number
    annual_pay: boolean
    mulch_color: string | null
    service_interval: string | null
    site_address: string | null
    sequence_order: number | null
    subdivision: string | null
    customer?: Customer
}

export interface Ledger {
    id: number
    billed: boolean | null
    cust_id: number
    work_date: string
    hours: number | null
    job_code: string | null
    job_notes: string | null
    work_type: string
    sheet_number: string | null
    times: string | null
    work_performed: string | null
    sheet_link: number | null
    customer?: Customer
    sheet?: Sheet
}

export interface AreaGroup {
    id: number
    area_name: string
    created_at?: string
    updated_at?: string
    deleted_at?: string | null
}

export interface ServiceSchedule {
    id: number
    crew_assigned: string | null
    crew_comments: string | null
    cust_id: number
    end_time: string | null
    service_notes: string | null
    service_status: string | null
    service_requested: string[] | null
    start_time: string | null
    crew_status: string | null
    site_address: string | null
    customer?: Customer
}

export interface Sheet {
    id: number
    begin_date: string
    end_date: string
    sheet_link: string | null
    ledgers_count?: number
}
