export type RoleType = '' | '*' | 'admin' | 'user';

export interface UserState {
  token?: string;
  coupon?: number;
  getPhone?: boolean;
  invitation?: { is_open?: boolean, invitation_id?: number };
  orderCount?: { payment?: number; delivery?: number; received?: number; comment?: number; };
  setting?: { points_name?: string, agent_open?: string };
  sign?: {};
  userInfo?: {
    user_id?: number;
    mpopen_id?: string;
    appopen_id?: string;
    union_id?: string;
    app_user?: string;
    reg_source?: string;
    nickName?: string;
    email?: string;
    mobile?: string;
    avatarUrl?: string;
    gender?: number;
    country?: string;
    province?: string;
    city?: string;
    address_id?: number;
    balance?: string;
    points?: string;
    pay_money?: string;
    expend_money?: string;
    grade_id?: number;
    referee_id?: number;
    total_points?: string;
    total_invite?: number;
    freeze_money?: string;
    cash_money?: string;
    real_name?: string;
    address: [],
    addressDefault: null,
    grade: {
      grade_id?: number;
      name?: string;
      open_money?: number;
      upgrade_money?: number;
      open_points?: number;
      upgrade_points?: number;
      open_invite?: number;
      upgrade_invite?: number;
      equity?: number;
      is_default?: number;
      remark?: string;
      weight?: number;
      give_points?: number;
      is_delete?: number;
      app_id?: number;
      create_time?: string;
      update_time?: string;
    }
  };
  address?: {};
}
