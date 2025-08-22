import { CyberButton } from "@/components/ui/cyber-button";
import { Home, Search, Plus, Trophy, User } from "lucide-react";

export const Navigation = () => {
  return (
    <nav className="fixed top-0 left-0 right-0 z-50 glass-card border-b border-card-border/30">
      <div className="max-w-7xl mx-auto px-6 py-4">
        <div className="flex items-center justify-between">
          {/* Logo */}
          <div className="flex items-center space-x-2">
            <div className="w-10 h-10 bg-gradient-hologram rounded-lg flex items-center justify-center">
              <Trophy className="w-6 h-6 text-background" />
            </div>
            <span className="text-xl font-orbitron font-bold text-gradient-cyber">
              TREASURE NEXUS
            </span>
          </div>
          
          {/* Navigation Links */}
          <div className="hidden md:flex items-center space-x-6">
            <CyberButton variant="ghost" size="sm">
              <Home className="w-4 h-4 mr-2" />
              Home
            </CyberButton>
            
            <CyberButton variant="ghost" size="sm">
              <Search className="w-4 h-4 mr-2" />
              Discover
            </CyberButton>
            
            <CyberButton variant="ghost" size="sm">
              <Plus className="w-4 h-4 mr-2" />
              Create
            </CyberButton>
            
            <CyberButton variant="ghost" size="sm">
              <Trophy className="w-4 h-4 mr-2" />
              Rankings
            </CyberButton>
          </div>
          
          {/* User Actions */}
          <div className="flex items-center space-x-4">
            <CyberButton variant="outline" size="sm">
              <User className="w-4 h-4 mr-2" />
              Sign In
            </CyberButton>
            
            <CyberButton variant="hologram" size="sm">
              Join Hunt
            </CyberButton>
          </div>
        </div>
      </div>
    </nav>
  );
};