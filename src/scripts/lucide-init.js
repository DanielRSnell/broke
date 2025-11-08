/**
 * Lucide Icons Initialization
 *
 * Initializes Lucide icons used throughout the BrokeHQ dashboard
 * Only imports icons that are actually used to minimize bundle size
 */

import { createIcons } from 'lucide';

// Import only the icons used in the dashboard
import {
  // Topbar
  PanelLeft,
  ChevronRight,
  ListFilter,
  LayoutGrid,
  Bell,
  Circle,

  // Sidebar
  X,
  Plus,
  Search,
  Inbox,
  User,
  LayoutList,
  Map,
  Building,
  Box,
  ChevronDown,
  Stethoscope,
  ListTodo,
  Activity,
  Archive,
  FolderKanban,
  Grid,
  Settings,

  // Task Row
  AlertTriangle,
  GitBranch,
  AppWindow,
  Check,
  ExternalLink,

  // Chart Panel
  MoreHorizontal,
  Gauge,
  Tag,

  // General
  Clock,
  CheckCircle2,
  AlertCircle,
  Users,
} from 'lucide';

// Initialize Lucide with only the icons we need
createIcons({
  icons: {
    // Topbar
    PanelLeft,
    ChevronRight,
    ListFilter,
    LayoutGrid,
    Bell,
    Circle,

    // Sidebar
    X,
    Plus,
    Search,
    Inbox,
    User,
    LayoutList,
    Map,
    Building,
    Box,
    ChevronDown,
    Stethoscope,
    ListTodo,
    Activity,
    Archive,
    FolderKanban,
    Grid,
    Settings,

    // Task Row
    AlertTriangle,
    GitBranch,
    AppWindow,
    Check,
    ExternalLink,

    // Chart Panel
    MoreHorizontal,
    Gauge,
    Tag,

    // General
    Clock,
    CheckCircle2,
    AlertCircle,
    Users,
  },
  // Custom attributes for all icons
  attrs: {
    'stroke-width': 2,
  },
  // Name transformation (kebab-case to PascalCase)
  nameAttr: 'data-lucide',
});

console.log('Lucide icons initialized successfully!');
