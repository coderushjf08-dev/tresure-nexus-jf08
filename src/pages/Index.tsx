import { Navigation } from "@/components/treasure-hunt/navigation";
import { HeroSection } from "@/components/treasure-hunt/hero-section";
import { HuntGrid } from "@/components/treasure-hunt/hunt-grid";
import { Leaderboard } from "@/components/treasure-hunt/leaderboard";

const Index = () => {
  return (
    <div className="min-h-screen bg-background relative">
      {/* Particle background */}
      <div className="particle-bg"></div>
      
      {/* Navigation */}
      <Navigation />
      
      {/* Hero Section */}
      <HeroSection />
      
      {/* Featured Hunts */}
      <HuntGrid />
      
      {/* Leaderboard */}
      <Leaderboard />
      
      {/* Footer */}
      <footer className="py-12 px-6 border-t border-card-border/30">
        <div className="max-w-7xl mx-auto text-center">
          <div className="text-gradient-hologram font-orbitron font-bold text-2xl mb-4">
            TREASURE NEXUS
          </div>
          <p className="text-muted-foreground font-rajdhani">
            © 2050 Treasure Nexus. The future of digital treasure hunting.
          </p>
        </div>
      </footer>
    </div>
  );
};

export default Index;